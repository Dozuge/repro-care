<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getCurrentUser()
    {
        return auth()->user();
    }

    private function getCurrentUserId(): ?int
    {
        $user = $this->getCurrentUser();
        return $user ? $user->id : null;
    }

    private function getCurrentUserType(): ?string
    {
        $user = $this->getCurrentUser();
        return $user ? $user->role : null;
    }

    private function getRoutePrefix(): string
    {
        $user = $this->getCurrentUser();

        return match ($user->role) {
            'midwife' => 'midwife',
            'bhw' => 'bhw',
            'bhw_president' => 'bhw-president',
            'user' => 'user',
            default => 'user',
        };
    }

    public function index()
    {
        $userId = $this->getCurrentUserId();
        $userType = $this->getCurrentUserType();
        if (!$userId || !$userType) {
            return redirect()->route('login');
        }

        $search = trim((string) request('search'));

        $messages = Message::with([
                'sender',
                'receiver',
                'replies.sender',
                'replies.receiver',
            ])
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->thread()
            ->withCount('replies')
            ->withMax('replies', 'created_at')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($messageQuery) use ($search) {
                    $messageQuery->where('subject', 'like', '%' . $search . '%')
                        ->orWhere('body', 'like', '%' . $search . '%')
                        ->orWhereHas('sender', fn ($q) => $q->where('name', 'like', '%' . $search . '%'))
                        ->orWhereHas('receiver', fn ($q) => $q->where('name', 'like', '%' . $search . '%'));
                });
            })
            ->orderByRaw('COALESCE(replies_max_created_at, created_at) DESC')
            ->paginate(15);

        $unreadCount = Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();

        $contacts = $this->getContacts($search);

        return view('messages.index', compact('messages', 'unreadCount', 'contacts', 'search'));
    }

    public function create(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $contacts = $this->getContacts($search);
        $receiverId = $request->query('to');
        $receiverRole = $request->query('role');
        $receiver = ($receiverId && $receiverRole)
            ? $this->findUserByRoleAndId($receiverRole, (int) $receiverId)
            : null;

        if ($receiver && !$this->canMessage($this->getCurrentUser(), $receiver)) {
            $receiver = null;
        }

        return view('messages.create', compact('contacts', 'receiver', 'search'));
    }

    public function send(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            return redirect()->route('login');
        }

        $request->validate([
            'receiver_id' => 'required|integer',
            'receiver_role' => 'required|in:woman,midwife,bhw,bhw_president',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string|max:2000',
            'reply_to_id' => 'nullable|exists:messages,id',
        ]);

        $receiver = $this->findUserByRoleAndId($request->receiver_role, (int) $request->receiver_id);
        if (!$receiver) {
            return back()->withErrors(['receiver_id' => 'The selected recipient is invalid.'])->withInput();
        }

        if (!$this->canMessage($currentUser, $receiver)) {
            throw ValidationException::withMessages([
                'receiver_id' => 'You are not allowed to message this user.',
            ]);
        }

        $message = Message::create([
            'sender_id' => $currentUser->id,
            'receiver_id' => (int) $request->receiver_id,
            'subject' => $request->subject,
            'body' => $request->body,
            'reply_to_id' => $request->reply_to_id,
        ]);

        \App\Models\Notification::createNotification(
            (int) $request->receiver_id,
            'New Message from ' . $currentUser->name . ': ' . \Illuminate\Support\Str::limit($request->body, 60),
            'New Message',
            'info',
            '/' . $this->getRoutePrefixForRole($request->receiver_role) . '/messages/' . ($request->reply_to_id ?: $message->id),
            $request->receiver_role === 'woman' ? 'user' : $request->receiver_role
        );

        $successMsg = 'Message sent successfully.';
        $prefix = $this->getRoutePrefix();

        if ($request->reply_to_id) {
            $successMsg = 'Reply sent.';
            return redirect()->route($prefix . '.messages.thread', $request->reply_to_id)
                ->with('success', $successMsg);
        }

        return redirect()->route($prefix . '.messages.thread', $message->id)
            ->with('success', $successMsg);
    }

    public function thread(int $id)
    {
        $userId = $this->getCurrentUserId();
        $userType = $this->getCurrentUserType();
        if (!$userId || !$userType) {
            return redirect()->route('login');
        }

        $root = Message::with([
                'sender',
                'receiver',
                'replies.sender',
                'replies.receiver',
            ])
            ->findOrFail($id);

        $isSender = $root->sender_id === $userId;
        $isReceiver = $root->receiver_id === $userId;

        if (!$isSender && !$isReceiver) {
            abort(403);
        }

        if ($isReceiver && !$root->is_read) {
            $root->markRead();
        }

        Message::where('reply_to_id', $root->id)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $contacts = $this->getContacts();

        return view('messages.thread', compact('root', 'contacts'));
    }

    public function markRead(int $id)
    {
        $userId = $this->getCurrentUserId();
        $userType = $this->getCurrentUserType();
        if (!$userId || !$userType) {
            return response()->json(['success' => false], 401);
        }

        $message = Message::where('receiver_id', $userId)
            ->findOrFail($id);

        $message->markRead();

        return response()->json(['success' => true]);
    }

    public function destroy(int $id)
    {
        $userId = $this->getCurrentUserId();
        $userType = $this->getCurrentUserType();
        if (!$userId || !$userType) {
            return redirect()->route('login');
        }

        $message = Message::findOrFail($id);

        $isSender = $message->sender_id === $userId;
        $isReceiver = $message->receiver_id === $userId;

        if (!$isSender && !$isReceiver) {
            abort(403);
        }

        $message->delete();

        return redirect()->route($this->getRoutePrefix() . '.messages.index')
            ->with('success', 'Message deleted.');
    }

    private function getContacts(?string $search = null): Collection
    {
        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            return collect([]);
        }

        $contacts = User::query()
            ->select(['id', 'first_name', 'middle_initial', 'last_name', 'role', 'status'])
            ->where('id', '!=', $currentUser->id)
            ->whereIn('role', ['user', 'midwife', 'bhw', 'bhw_president'])
            ->where(function ($query) {
                $query->where('role', '!=', 'user')
                    ->orWhere(function ($userQuery) {
                        $userQuery->where('role', 'user')
                            ->where(function ($approvedQuery) {
                                $approvedQuery->whereNull('status')
                                    ->orWhere('status', 'approved');
                            });
                    });
            })
            ->get()
            ->filter(fn (User $user) => $this->canMessage($currentUser, $user))
            ->map(function (User $user) {
                $messagingRole = $this->normalizeUserRoleForMessaging($user->role);

                return (object) [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $messagingRole,
                    'type' => $messagingRole,
                    'user_role' => $user->role,
                    'label' => $this->formatRoleLabel($user->role),
                ];
            });

        return $this->filterContacts($contacts, $search);
    }

    private function filterContacts(\Illuminate\Support\Collection $contacts, ?string $search): \Illuminate\Support\Collection
    {
        if (!$search) {
            return $contacts->sortBy('name')->values();
        }

        $needle = mb_strtolower($search);

        return $contacts->filter(function ($contact) use ($needle) {
            return str_contains(mb_strtolower($contact->name), $needle)
                || str_contains(mb_strtolower($contact->role), $needle)
                || str_contains(mb_strtolower($contact->label ?? ''), $needle);
        })->sortBy('name')->values();
    }

    private function findUserByRoleAndId(string $role, int $id)
    {
        return match ($role) {
            'woman' => User::where('role', 'user')->find($id),
            'midwife' => User::where('role', 'midwife')->find($id),
            'bhw' => User::where('role', 'bhw')->find($id),
            'bhw_president' => User::where('role', 'bhw_president')->find($id),
            default => null,
        };
    }


    private function getRoutePrefixForRole(string $role): string
    {
        return match ($role) {
            'midwife' => 'midwife',
            'bhw' => 'bhw',
            'bhw_president' => 'bhw-president',
            default => 'user',
        };
    }

    private function canMessage(?User $sender, ?User $receiver): bool
    {
        if (!$sender || !$receiver) {
            return false;
        }

        if ($sender->id === $receiver->id) {
            return false;
        }

        if ($sender->role === 'user') {
            return in_array($receiver->role, ['midwife', 'bhw', 'bhw_president'], true);
        }

        return in_array($sender->role, ['midwife', 'bhw', 'bhw_president'], true)
            && in_array($receiver->role, ['user', 'midwife', 'bhw', 'bhw_president'], true);
    }

    private function normalizeUserRoleForMessaging(string $role): string
    {
        return $role === 'user' ? 'woman' : $role;
    }

    private function formatRoleLabel(string $role): string
    {
        return match ($role) {
            'user' => 'Patient',
            'midwife' => 'Midwife',
            'bhw_president' => 'BHW President',
            'bhw' => 'BHW',
            default => ucfirst(str_replace('_', ' ', $role)),
        };
    }
}
