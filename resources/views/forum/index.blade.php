@php
    $forumUser = auth()->user();
    $forumLayout = $forumUser?->role === 'bhw_president' ? 'bhw-president.layout' : 'user.layout';
    $forumSection = $forumUser?->role === 'bhw_president' ? 'bhw-president-content' : 'user-content';
    $recentPosts = \App\Models\ForumPost::active()->latest()->take(5)->get();
    $forumRoleLabel = match($forumUser?->role) {
        'midwife' => 'Midwife',
        'bhw' => 'BHW',
        'bhw_president' => 'BHW President',
        default => 'Member',
    };
@endphp

@extends($forumLayout)

@section('title', 'Community Forum - ReproCare')

@push('styles')
<style>
    .forum-feed-card {
        border-radius: 24px;
        border: 1px solid var(--border);
        background: var(--bg-card);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .forum-feed-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .forum-avatar-shell {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        overflow: hidden;
        flex-shrink: 0;
        background: linear-gradient(135deg, var(--primary), var(--accent-violet));
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        box-shadow: 0 10px 24px rgba(139, 92, 246, 0.22);
    }
    .forum-avatar-shell img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .forum-content-copy {
        white-space: pre-wrap;
        line-height: 1.75;
        color: var(--text);
    }
    .forum-image-block {
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid var(--border);
        margin-top: 1rem;
    }
    .forum-image-block img {
        width: 100%;
        max-height: 320px;
        object-fit: cover;
        display: block;
    }
    .forum-action-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        border-top: 1px solid var(--border);
        margin-top: 1rem;
        padding-top: 1rem;
    }
    .forum-action-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
    }
    .forum-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.5rem 0.85rem;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: transparent;
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.84rem;
        transition: all var(--transition-fast);
    }
    .forum-action-btn:hover {
        color: var(--primary-light);
        border-color: var(--primary);
        background: var(--primary-subtle);
    }
    .forum-action-btn.liked {
        color: var(--secondary);
        border-color: rgba(244, 63, 94, 0.3);
        background: rgba(244, 63, 94, 0.1);
    }
    .forum-comment-panel {
        margin-top: 1rem;
        padding: 1rem;
        border-radius: 18px;
        border: 1px solid var(--border);
        background: var(--bg-card2);
    }
    .forum-comment-item {
        display: flex;
        gap: 0.8rem;
    }
    .forum-comment-item + .forum-comment-item {
        margin-top: 0.9rem;
    }
    .forum-comment-avatar {
        width: 32px;
        height: 32px;
        border-radius: 12px;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--accent-violet), var(--primary));
        color: #fff;
        font-size: 0.8rem;
        font-weight: 800;
    }
    .forum-comment-bubble {
        flex: 1;
        border-radius: 16px;
        border: 1px solid var(--border);
        background: var(--bg-card);
        padding: 0.8rem 0.9rem;
    }
</style>
@endpush

@section($forumSection)
<div class="workspace-stack">
    <div class="page-hero fade-in-card">
        <div class="workspace-toolbar" style="position:relative;z-index:1;">
            <div>
                <div class="page-hero-title">
                    <i class="bi bi-chat-heart-fill me-2"></i>Community Forum
                </div>
                <p class="page-hero-subtitle">Ask questions, share experiences, and support the reproductive health community together.</p>
            </div>
            <div class="workspace-toolbar-actions">
                <span class="summary-chip chip-info"><i class="bi bi-person-badge"></i>{{ $forumRoleLabel }}</span>
                <a href="{{ route('forum.create') }}" class="btn-hero-primary">
                    <i class="bi bi-plus-circle-fill"></i>Start a Post
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="workspace-stack">
                <div class="workspace-panel fade-in-card">
                    <div class="workspace-panel-body">
                        <div class="workspace-toolbar">
                            <div>
                                <h2 class="workspace-panel-title mb-0"><i class="bi bi-layout-text-window"></i>Discussion Feed</h2>
                                <p class="workspace-panel-subtitle">Switch between all posts and the ones you started.</p>
                            </div>
                            <div class="section-chip-row">
                                <a href="{{ route('forum.index') }}" class="summary-chip {{ !$filter ? 'chip-primary' : 'chip-info' }}">All Posts</a>
                                <a href="{{ route('forum.index', ['filter' => 'my-posts']) }}" class="summary-chip {{ $filter === 'my-posts' ? 'chip-primary' : 'chip-info' }}">My Posts</a>
                            </div>
                        </div>
                    </div>
                </div>

                @if($posts->count() > 0)
                    @foreach($posts as $post)
                        @php
                            $roleChip = match($post->user_type) {
                                'midwife' => 'chip-primary',
                                'bhw' => 'chip-info',
                                'bhw_president' => 'chip-warning',
                                default => 'chip-success',
                            };
                            $roleName = match($post->user_type) {
                                'midwife' => 'Midwife',
                                'bhw' => 'BHW',
                                'bhw_president' => 'BHW President',
                                default => 'Member',
                            };
                        @endphp
                        <div class="forum-feed-card fade-in-card">
                            <div class="workspace-panel-body">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div class="d-flex gap-3">
                                        <div class="forum-avatar-shell">
                                            @if(optional($post->user)->profile_image_url)
                                                <img src="{{ $post->user->profile_image_url }}" alt="{{ optional($post->user)->name }}">
                                            @else
                                                {{ strtoupper(substr(optional($post->user)->name ?? 'U', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <div class="table-title">{{ optional($post->user)->name ?? 'Unknown User' }}</div>
                                                <span class="summary-chip {{ $roleChip }}">{{ $roleName }}</span>
                                                <span class="summary-chip {{ $post->status === 'active' ? 'chip-success' : 'chip-danger' }}">
                                                    {{ ucfirst($post->status) }}
                                                </span>
                                            </div>
                                            <div class="table-subtitle">
                                                <i class="bi bi-clock me-1"></i>{{ $post->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>

                                    @if(auth()->id() === $post->user_id)
                                        <div class="table-actions">
                                            <a href="{{ route('forum.edit', $post->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('forum.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Delete this post?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                <div class="forum-content-copy mt-3">{{ $post->content }}</div>

                                @if($post->post_image)
                                    <a href="{{ route('forum.show', $post->id) }}" class="forum-image-block d-block">
                                        <img src="{{ $post->post_image_url }}" alt="Forum post image">
                                    </a>
                                @endif

                                <div class="forum-action-row">
                                    <div class="forum-action-list">
                                        <form action="{{ route('forum.like', $post->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="forum-action-btn {{ $post->isLikedBy(auth()->id(), \App\Models\User::class) ? 'liked' : '' }}">
                                                <i class="bi {{ $post->isLikedBy(auth()->id(), \App\Models\User::class) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                                {{ $post->likes_count }}
                                            </button>
                                        </form>
                                        <button type="button" class="forum-action-btn" data-bs-toggle="collapse" data-bs-target="#comments-{{ $post->id }}">
                                            <i class="bi bi-chat-dots"></i>{{ $post->comments_count }}
                                        </button>
                                        <a href="{{ route('forum.show', $post->id) }}" class="forum-action-btn">
                                            <i class="bi bi-box-arrow-up-right"></i>Open
                                        </a>
                                    </div>
                                    <div class="table-subtitle">{{ $post->comments_count }} comments</div>
                                </div>

                                <div class="collapse" id="comments-{{ $post->id }}">
                                    <div class="forum-comment-panel">
                                        <form action="{{ route('forum.comment', $post->id) }}" method="POST" class="mb-3">
                                            @csrf
                                            <div class="input-group">
                                                <input type="text" name="content" class="form-control" placeholder="Add a helpful comment..." required>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-send-fill"></i>
                                                </button>
                                            </div>
                                        </form>

                                        @forelse($post->comments as $comment)
                                            <div class="forum-comment-item">
                                                <div class="forum-comment-avatar">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</div>
                                                <div class="forum-comment-bubble">
                                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                                        <div class="table-title" style="font-size:0.92rem;">{{ $comment->user->name }}</div>
                                                        <div class="table-subtitle">{{ $comment->created_at->diffForHumans() }}</div>
                                                    </div>
                                                    <div class="mt-2 text-muted">{{ $comment->content }}</div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center text-muted py-3">No comments yet. Start the conversation.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-center">
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="workspace-panel fade-in-card">
                        <div class="empty-state-panel">
                            <i class="bi bi-chat-square-heart"></i>
                            <h3>No posts yet</h3>
                            <p>{{ $filter === 'my-posts' ? 'You have not started any conversations yet.' : 'The community feed is still quiet. Be the first to start a helpful discussion.' }}</p>
                            <a href="{{ route('forum.create') }}" class="btn btn-primary mt-3">Create First Post</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-xl-4">
            <div class="workspace-stack">
                <div class="workspace-panel fade-in-card">
                    <div class="workspace-panel-header">
                        <h2 class="workspace-panel-title"><i class="bi bi-bar-chart-line-fill"></i>Forum Snapshot</h2>
                    </div>
                    <div class="workspace-panel-body">
                        <div class="info-strip">
                            <div class="info-pill-card">
                                <div class="info-pill-label"><i class="bi bi-chat-dots"></i>Total Posts</div>
                                <div class="info-pill-value">{{ \App\Models\ForumPost::count() }}</div>
                            </div>
                            <div class="info-pill-card">
                                <div class="info-pill-label"><i class="bi bi-person-lines-fill"></i>My Posts</div>
                                <div class="info-pill-value">{{ \App\Models\ForumPost::where('user_id', auth()->id())->count() }}</div>
                            </div>
                            <div class="info-pill-card">
                                <div class="info-pill-label"><i class="bi bi-calendar-day"></i>Today</div>
                                <div class="info-pill-value">{{ \App\Models\ForumPost::whereDate('created_at', today())->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="insight-card fade-in-card">
                    <h6>Community Guidelines</h6>
                    <div class="insight-list">
                        <div class="insight-list-item"><i class="bi bi-shield-check"></i><span>Keep discussions respectful, supportive, and free from personal attacks.</span></div>
                        <div class="insight-list-item"><i class="bi bi-lock"></i><span>Protect patient privacy and avoid posting sensitive identifying details.</span></div>
                        <div class="insight-list-item"><i class="bi bi-patch-check"></i><span>Share experiences and general education, but direct urgent concerns to health professionals.</span></div>
                    </div>
                </div>

                <div class="workspace-panel fade-in-card">
                    <div class="workspace-panel-header">
                        <h2 class="workspace-panel-title"><i class="bi bi-clock-history"></i>Recent Activity</h2>
                    </div>
                    <div class="workspace-panel-body">
                        @forelse($recentPosts as $recent)
                            <a href="{{ route('forum.show', $recent->id) }}" class="d-block text-decoration-none p-3 rounded-4 mb-2" style="background:var(--bg-card2);border:1px solid var(--border);">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <span class="table-title" style="font-size:0.9rem;">{{ $recent->user->name }}</span>
                                    <span class="table-subtitle">{{ $recent->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="table-subtitle mt-2">{{ \Illuminate\Support\Str::limit($recent->content, 72) }}</div>
                            </a>
                        @empty
                            <div class="text-center text-muted py-3">No recent activity yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
