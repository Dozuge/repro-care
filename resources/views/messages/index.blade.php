@extends(request()->routeIs('midwife.messages.*') ? 'midwife.layout' : (request()->routeIs('bhw.messages.*') ? 'bhw.layout' : (request()->routeIs('bhw-president.messages.*') ? 'bhw-president.layout' : 'user.layout')))

@section('title', 'Messages - ReproCare')

@push('styles')
<style>
    body { padding-top: 70px !important; }
    .chat-shell {
        display: grid;
        grid-template-columns: minmax(320px, 380px) minmax(0, 1fr);
        gap: 1.25rem;
    }
    .chat-panel {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 24px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }
    .chat-panel-header {
        padding: 1.25rem 1.35rem;
        border-bottom: 1px solid var(--border);
        background: linear-gradient(135deg, rgba(61, 102, 255, 0.12), rgba(17, 24, 39, 0.04));
    }
    .chat-panel-title {
        margin: 0;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--text);
    }
    .chat-panel-subtitle {
        margin-top: 0.35rem;
        font-size: 0.82rem;
        color: var(--text-muted);
    }
    .chat-search {
        width: 100%;
        background: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: 14px;
        color: var(--text);
        padding: 0.85rem 1rem;
        outline: none;
    }
    .chat-search:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }
    .conversation-list {
        padding: 0.75rem;
        max-height: 72vh;
        overflow-y: auto;
    }
    .conversation-item {
        display: block;
        padding: 0.95rem;
        border: 1px solid transparent;
        border-radius: 18px;
        text-decoration: none;
        color: var(--text);
        transition: 0.18s ease;
        margin-bottom: 0.6rem;
    }
    .conversation-item:hover {
        background: rgba(61, 102, 255, 0.05);
        border-color: rgba(61, 102, 255, 0.12);
        transform: translateY(-1px);
    }
    .conversation-item.unread {
        background: rgba(61, 102, 255, 0.07);
        border-color: rgba(61, 102, 255, 0.16);
    }
    .conversation-row {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }
    .avatar-pill {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #fff;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }
    .conversation-meta {
        min-width: 0;
        flex: 1;
    }
    .conversation-topline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.25rem;
    }
    .conversation-name {
        font-weight: 700;
        color: var(--text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .conversation-time {
        font-size: 0.76rem;
        color: var(--text-muted);
        flex-shrink: 0;
    }
    .conversation-preview {
        font-size: 0.83rem;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .contact-grid {
        padding: 1rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 0.85rem;
    }
    .contact-card {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 1rem;
        border-radius: 18px;
        border: 1px solid var(--border);
        text-decoration: none;
        color: var(--text);
        background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(61, 102, 255, 0.04));
        transition: 0.18s ease;
    }
    .contact-card:hover {
        transform: translateY(-1px);
        border-color: rgba(61, 102, 255, 0.2);
        box-shadow: var(--shadow-sm);
    }
    .contact-role {
        display: inline-flex;
        margin-top: 0.2rem;
        font-size: 0.72rem;
        color: var(--text-muted);
        background: var(--bg-card2);
        border-radius: 999px;
        padding: 0.18rem 0.55rem;
    }
    .empty-state {
        padding: 3rem 1.25rem;
        text-align: center;
        color: var(--text-muted);
    }
    @media (max-width: 991.98px) {
        .chat-shell {
            grid-template-columns: 1fr;
        }
        .conversation-list {
            max-height: none;
        }
    }
</style>
@endpush

@php
    $contentSection = request()->routeIs('midwife.messages.*') ? 'midwife-content' : (request()->routeIs('bhw.messages.*') ? 'bhw-content' : (request()->routeIs('bhw-president.messages.*') ? 'bhw-president-content' : 'user-content'));
    $messagesRouteBase = request()->routeIs('midwife.messages.*') ? 'midwife.messages' : (request()->routeIs('bhw.messages.*') ? 'bhw.messages' : (request()->routeIs('bhw-president.messages.*') ? 'bhw-president.messages' : 'user.messages'));
    $currentUser = auth()->user();
    $currentUserId = $currentUser?->id;
    $currentUserRole = $currentUser?->role;
@endphp

@section($contentSection)
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h2 class="mb-1" style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;color:var(--text);">
            <i class="bi bi-chat-dots-fill me-2"></i>Messages
        </h2>
        <div style="color:var(--text-muted);font-size:0.9rem;">
            Modern chat for patients, midwives, BHWs, and BHW presidents.
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if($unreadCount > 0)
            <span class="badge rounded-pill text-bg-primary px-3 py-2">{{ $unreadCount }} unread</span>
        @endif
        <a href="{{ route($messagesRouteBase . '.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> New Chat
        </a>
    </div>
</div>

<div class="chat-shell">
    <section class="chat-panel">
        <div class="chat-panel-header">
            <h3 class="chat-panel-title">Conversations</h3>
            <div class="chat-panel-subtitle">Your latest threads and unread messages.</div>
            <form method="GET" class="mt-3">
                <input type="search" name="search" class="chat-search" placeholder="Search conversations or contacts..." value="{{ $search ?? '' }}">
            </form>
        </div>

        <div class="conversation-list">
            @forelse($messages as $msg)
                @php
                    $sentByCurrentUser = $msg->sender_id === $currentUserId;
                    $unreadForCurrentUser = !$msg->is_read && $msg->receiver_id === $currentUserId;
                    $otherParty = $sentByCurrentUser ? $msg->receiver : $msg->sender;
                    $lastActivity = $msg->replies_max_created_at ? \Carbon\Carbon::parse($msg->replies_max_created_at) : $msg->created_at;
                    $previewPrefix = $sentByCurrentUser ? 'You: ' : '';
                @endphp
                <a href="{{ route($messagesRouteBase . '.thread', $msg->id) }}" class="conversation-item {{ $unreadForCurrentUser ? 'unread' : '' }}">
                    <div class="conversation-row">
                        <div class="avatar-pill">{{ strtoupper(substr(optional($otherParty)->name ?? 'U', 0, 1)) }}</div>
                        <div class="conversation-meta">
                            <div class="conversation-topline">
                                <div class="conversation-name">{{ optional($otherParty)->name ?? 'Unknown User' }}</div>
                                <div class="conversation-time">{{ $lastActivity->diffForHumans() }}</div>
                            </div>
                            <div class="conversation-preview">
                                {{ $previewPrefix }}{{ \Illuminate\Support\Str::limit($msg->body, 72) }}
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                                @if($msg->replies_count > 0)
                                    <span class="badge text-bg-light">{{ $msg->replies_count + 1 }} messages</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state">
                    <i class="bi bi-chat-square-text" style="font-size:2.6rem;"></i>
                    <div class="mt-3 fw-semibold">No conversations yet</div>
                    <div class="mt-1">Start a new chat with one of your allowed contacts.</div>
                </div>
            @endforelse
        </div>
    </section>

    <section class="chat-panel">
        <div class="chat-panel-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="chat-panel-title">Allowed Contacts</h3>
                <div class="chat-panel-subtitle">
                    @if($currentUserRole === 'user')
                        Patients can message midwives, BHWs, and BHW presidents only.
                    @else
                        Staff can message anyone in the system.
                    @endif
                </div>
            </div>
            <a href="{{ route($messagesRouteBase . '.create') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-pencil-square me-1"></i> Compose
            </a>
        </div>

        @if($contacts->isEmpty())
            <div class="empty-state">
                <i class="bi bi-people" style="font-size:2.6rem;"></i>
                <div class="mt-3 fw-semibold">No contacts available</div>
                <div class="mt-1">There are no users you can message right now.</div>
            </div>
        @else
            <div class="contact-grid">
                @foreach($contacts as $contact)
                    <a href="{{ route($messagesRouteBase . '.create', ['to' => $contact->id, 'role' => $contact->role]) }}" class="contact-card">
                        <div class="avatar-pill">{{ strtoupper(substr($contact->name, 0, 1)) }}</div>
                        <div>
                            <div class="fw-semibold">{{ $contact->name }}</div>
                            <span class="contact-role">{{ $contact->label }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
