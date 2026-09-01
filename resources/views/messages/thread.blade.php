@extends(request()->routeIs('midwife.messages.*') ? 'midwife.layout' : (request()->routeIs('bhw.messages.*') ? 'bhw.layout' : (request()->routeIs('bhw-president.messages.*') ? 'bhw-president.layout' : 'user.layout')))

@section('title', 'Conversation - ReproCare')

@push('styles')
<style>
    body { padding-top: 70px !important; }
    .chat-thread {
        max-width: 980px;
        margin: 0 auto;
    }
    .thread-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 26px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
    }
    .thread-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.2rem 1.4rem;
        border-bottom: 1px solid var(--border);
        background: linear-gradient(135deg, rgba(61, 102, 255, 0.12), rgba(17, 24, 39, 0.04));
    }
    .thread-user {
        display: flex;
        align-items: center;
        gap: 0.9rem;
    }
    .thread-avatar {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: #fff;
        font-weight: 800;
    }
    .thread-name {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        color: var(--text);
        margin: 0;
        font-size: 1rem;
    }
    .thread-role {
        margin-top: 0.18rem;
        color: var(--text-muted);
        font-size: 0.8rem;
    }
    .message-stream {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 1.25rem;
        min-height: 55vh;
        max-height: 65vh;
        overflow-y: auto;
        background:
            radial-gradient(circle at top right, rgba(61, 102, 255, 0.08), transparent 26%),
            radial-gradient(circle at bottom left, rgba(61, 102, 255, 0.05), transparent 24%);
    }
    .message-row {
        display: flex;
    }
    .message-row.mine {
        justify-content: flex-end;
    }
    .message-bubble {
        max-width: min(76%, 620px);
        border-radius: 22px;
        padding: 0.9rem 1rem;
        position: relative;
    }
    .message-row.mine .message-bubble {
        color: #fff;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-bottom-right-radius: 8px;
    }
    .message-row.theirs .message-bubble {
        color: var(--text);
        background: var(--bg-card2);
        border: 1px solid var(--border);
        border-bottom-left-radius: 8px;
    }
    .message-body {
        white-space: pre-wrap;
        line-height: 1.55;
        font-size: 0.93rem;
    }
    .message-meta {
        margin-top: 0.4rem;
        font-size: 0.75rem;
        opacity: 0.78;
    }
    .composer {
        border-top: 1px solid var(--border);
        padding: 1rem 1.2rem 1.2rem;
        background: var(--bg-card);
    }
    .composer-box {
        background: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: 22px;
        padding: 0.9rem;
    }
    .composer-textarea {
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        color: var(--text);
        resize: none;
        min-height: 92px;
        font-size: 0.94rem;
    }
    .composer-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 0.75rem;
        flex-wrap: wrap;
    }
    .schedule-wrap {
        display: none;
        margin-top: 0.75rem;
    }
    .schedule-wrap.active {
        display: block;
    }
    .schedule-input {
        max-width: 280px;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        color: var(--text);
        padding: 0.65rem 0.8rem;
    }
    @media (max-width: 767.98px) {
        .message-bubble {
            max-width: 90%;
        }
        .thread-topbar {
            align-items: flex-start;
            flex-direction: column;
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

    $rootSentByCurrentUser = $root->sender_id === $currentUserId;

    $otherParty = $rootSentByCurrentUser ? $root->receiver : $root->sender;
    $otherPartyLabel = match ($otherParty?->role) {
        'user' => 'Patient',
        'midwife' => 'Midwife',
        'bhw_president' => 'BHW President',
        'bhw' => 'BHW',
        default => 'User',
    };
@endphp

@section($contentSection)
<div class="chat-thread">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <a href="{{ route($messagesRouteBase . '.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Messages
        </a>
        <a href="{{ route($messagesRouteBase . '.create', ['to' => $otherParty?->id, 'role' => $rootSentByCurrentUser ? $root->receiver_role : $root->sender_role]) }}" class="btn btn-outline-primary">
            <i class="bi bi-plus-lg me-1"></i> New Chat
        </a>
    </div>

    <div class="thread-card">
        <div class="thread-topbar">
            <div class="thread-user">
                <div class="thread-avatar">{{ strtoupper(substr(optional($otherParty)->name ?? 'U', 0, 1)) }}</div>
                <div>
                    <h3 class="thread-name">{{ optional($otherParty)->name ?? 'Unknown User' }}</h3>
                    <div class="thread-role">{{ $otherPartyLabel }}</div>
                </div>
            </div>
            @if($root->subject)
                <div class="text-muted small">
                    <strong>Subject:</strong> {{ $root->subject }}
                </div>
            @endif
        </div>

        <div class="message-stream" id="messageStream">
            <div class="message-row {{ $rootSentByCurrentUser ? 'mine' : 'theirs' }}">
                <div class="message-bubble">
                    <div class="message-body">{{ $root->body }}</div>
                    <div class="message-meta">
                        {{ $rootSentByCurrentUser ? 'You' : optional($root->sender)->name }}
                        · {{ $root->created_at->format('M j, Y g:i A') }}
                    </div>
                </div>
            </div>

            @foreach($root->replies->sortBy('created_at') as $reply)
                @php
                    $replySentByCurrentUser = $reply->sender_id === $currentUserId;
                @endphp
                <div class="message-row {{ $replySentByCurrentUser ? 'mine' : 'theirs' }}">
                    <div class="message-bubble">
                        <div class="message-body">{{ $reply->body }}</div>
                        <div class="message-meta">
                            {{ $replySentByCurrentUser ? 'You' : optional($reply->sender)->name }}
                            · {{ $reply->created_at->format('M j, Y g:i A') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="composer">
            <form action="{{ route($messagesRouteBase . '.send') }}" method="POST">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $rootSentByCurrentUser ? $root->receiver_id : $root->sender_id }}">
                <input type="hidden" name="receiver_role" value="{{ $rootSentByCurrentUser ? $root->receiver_role : $root->sender_role }}">
                <input type="hidden" name="reply_to_id" value="{{ $root->id }}">

                <div class="composer-box">
                    <textarea name="body" class="composer-textarea" placeholder="Type a message..." required>{{ old('body') }}</textarea>
                </div>

                <div class="composer-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send-fill me-1"></i> Send
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const stream = document.getElementById('messageStream');
    if (stream) {
        stream.scrollTop = stream.scrollHeight;
    }
});
</script>
@endsection
