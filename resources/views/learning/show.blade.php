@php
    $learningUser = auth()->user();
    $learningLayout = match($learningUser?->role) {
        'midwife' => 'midwife.layout',
        'bhw' => 'bhw.layout',
        'bhw_president' => 'bhw-president.layout',
        default => 'user.layout',
    };
    $learningSection = match($learningUser?->role) {
        'midwife' => 'midwife-content',
        'bhw' => 'bhw-content',
        'bhw_president' => 'bhw-president-content',
        default => 'user-content',
    };
@endphp

@extends($learningLayout)

@section('title', $material->title . ' - ReproCare')

@push('styles')
<style>
    .material-hero {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .material-banner { width: 100%; height: 220px; object-fit: cover; }
    .material-body { padding: 2rem; }
    .material-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.6rem; font-weight: 800; color: var(--text); margin-bottom: 1rem; line-height: 1.3; }
    .material-prose { font-size: 0.95rem; line-height: 1.8; color: var(--text); white-space: pre-wrap; }
    .video-embed { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 16px; margin-bottom: 1.5rem; }
    .video-embed iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
    .category-pill { background: var(--primary-subtle); color: var(--primary-light); padding: 0.25em 0.85em; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; }
    .meta-row { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.25rem; font-size: 0.8rem; color: var(--text-muted); }
    .quiz-cta { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-radius: 16px; padding: 1.5rem; color: #fff; text-align: center; margin-top: 1.5rem; }
    .quiz-cta h5 { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; margin-bottom: 0.5rem; }
    .btn-quiz { background: #fff; color: var(--primary-dark); border: none; border-radius: 12px; font-weight: 700; padding: 0.65rem 2rem; font-size: 0.9rem; transition: transform 0.2s; }
    .btn-quiz:hover { transform: translateY(-2px); color: var(--primary-dark); }
</style>
@endpush

@section($learningSection)

<div class="d-flex align-items-center gap-3 mb-4 fade-in-card">
    <a href="{{ $learningUser?->role === 'midwife' ? route('midwife.learning.index') : route('learning.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
    <div>
        <h1 class="page-title mb-0">{{ $material->title }}</h1>
        <p class="page-subtitle mb-0">Learning Material</p>
    </div>
</div>

<div class="material-hero fade-in-card">

    {{-- Banner image if available --}}
    @if($material->image_url)
        <img src="{{ $material->image_url }}"
             alt="{{ $material->title }}"
             class="material-banner"
             onerror="this.style.display='none'">
    @endif

    <div class="material-body">

        {{-- Meta --}}
        <div class="meta-row">
            <span class="category-pill">{{ ucfirst($material->category ?? 'General') }}</span>
            @php
                $typeConfig = [
                    'article' => ['bg' => '#6366f1', 'icon' => 'bi-file-text', 'label' => 'Article'],
                    'link'    => ['bg' => '#10b981', 'icon' => 'bi-link-45deg', 'label' => 'Link'],
                    'file'    => ['bg' => '#f59e0b', 'icon' => 'bi-file-earmark', 'label' => 'File'],
                    'video'   => ['bg' => '#ef4444', 'icon' => 'bi-play-circle', 'label' => 'Video'],
                    'quiz'    => ['bg' => '#8b5cf6', 'icon' => 'bi-patch-question', 'label' => 'Quiz'],
                ];
                $tc = $typeConfig[$material->material_type] ?? $typeConfig['article'];
            @endphp
            <span style="background:{{ $tc['bg'] }}20;color:{{ $tc['bg'] }};padding:0.25em 0.85em;border-radius:20px;font-size:0.75rem;font-weight:700;">
                <i class="bi {{ $tc['icon'] }} me-1"></i>{{ $tc['label'] }}
            </span>
            @if($material->week_number)
                <span style="background:rgba(236,72,153,0.12);color:#f472b6;padding:0.25em 0.85em;border-radius:20px;font-size:0.75rem;font-weight:700;">
                    <i class="bi bi-calendar-heart me-1"></i>Week {{ $material->week_number }}
                </span>
            @endif
            <span><i class="bi bi-calendar3 me-1"></i>{{ $material->created_at->format('F j, Y') }}</span>
        </div>

        <h2 class="material-title">{{ $material->title }}</h2>

        {{-- VIDEO --}}
        @if($material->material_type === 'video')
            @if($material->embed_url)
                <div class="video-embed mb-3">
                    <iframe src="{{ $material->embed_url }}"
                            title="{{ $material->title }}"
                            allowfullscreen
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                </div>
            @elseif($material->video_url)
                <div class="mb-3 text-center">
                    <a href="{{ $material->video_url }}" target="_blank" class="btn btn-danger btn-lg">
                        <i class="bi bi-play-circle-fill me-2"></i> Watch Video
                    </a>
                </div>
            @endif
        @endif

        {{-- LINK --}}
        @if($material->material_type === 'link' && $material->link_url)
            <div class="alert" style="background:rgba(14,165,233,0.1);border:1px solid rgba(14,165,233,0.3);border-radius:14px;margin-bottom:1.25rem;">
                <i class="bi bi-box-arrow-up-right me-2" style="color:#38bdf8;"></i>
                External resource:
                <a href="{{ $material->link_url }}" target="_blank" style="color:#38bdf8;font-weight:600;">
                    {{ $material->link_url }}
                </a>
            </div>
        @endif

        {{-- Content --}}
        @if($material->material_type !== 'quiz')
            <div class="material-prose">{{ $material->content }}</div>
        @else
            <div class="material-prose mb-2">{{ $material->content }}</div>
        @endif

        {{-- QUIZ CTA --}}
        @if($material->material_type === 'quiz' && $material->quiz_data)
            <div class="quiz-cta">
                <h5><i class="bi bi-patch-question-fill me-2"></i>Ready to Test Your Knowledge?</h5>
                <p style="font-size:0.9rem;opacity:0.85;margin-bottom:1rem;">
                    {{ count($material->quiz_data) }} question(s) · Take the quiz now
                </p>
                <a href="{{ route('learning.show', $material->id) }}#quiz-section" class="btn-quiz">
                    <i class="bi bi-play-fill me-1"></i> Start Quiz
                </a>
            </div>
            {{-- Inline quiz --}}
            <div id="quiz-section" class="mt-4">
                <a href="{{ route('learning.quiz.submit', $material->id) }}"
                   class="btn btn-primary w-100"
                   onclick="this.href='{{ route('learning.show', $material->id) }}'">
                </a>
                {{-- Route to quiz view instead --}}
            </div>
            <div class="mt-3 text-center">
                <a href="{{ url('/learning/' . $material->id . '/quiz') }}" class="btn btn-primary px-5">
                    <i class="bi bi-patch-question-fill me-2"></i> Take Quiz
                </a>
            </div>
        @endif

        {{-- FILE download --}}
        @if($material->material_type === 'file')
            <div class="text-center mt-3">
                <a href="{{ $material->file_url ?: '#' }}" class="btn btn-warning px-4" {{ $material->file_url ? 'target=_blank' : '' }}>
                    <i class="bi bi-download me-2"></i> Download File
                </a>
            </div>
        @endif
    </div>
</div>

@endsection
