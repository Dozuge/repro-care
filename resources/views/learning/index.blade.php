@php
    $learningUser = auth()->user();
    $learningLayout = match($learningUser?->role) {
        'cho'           => 'cho.layout',
        'midwife'       => 'midwife.layout',
        'bhw'           => 'bhw.layout',
        'bhw_president' => 'bhw-president.layout',
        default         => 'user.layout',
    };
    $learningSection = match($learningUser?->role) {
        'cho'           => 'cho-content',
        'midwife'       => 'midwife-content',
        'bhw'           => 'bhw-content',
        'bhw_president' => 'bhw-president-content',
        default         => 'user-content',
    };
    $activeFilter = request('type');
    $activeCategory = request('category');
@endphp

@extends($learningLayout)

@section('title', 'Media & Learning Library - ReproCare')

@section($learningSection)

{{-- Header Banner --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-800 mb-1" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text); letter-spacing:-0.5px;">
            <i class="bi bi-camera-video-fill me-2 text-primary"></i>Embedded Playable Media &amp; Learning Center
        </h2>
        <p class="text-muted mb-0" style="font-size:0.9rem;">
            Streaming video guidance, clinical counseling materials, and healthcare worker (HCW) training modules.
        </p>
    </div>
    @if($learningUser?->isMidwife() || $learningUser?->isCho())
        <div class="d-flex gap-2">
            <a href="{{ route('midwife.learning.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5 shadow-sm" style="border-radius:10px; font-weight:600;">
                <i class="bi bi-plus-lg"></i> Add New Video / Material
            </a>
            @if($learningUser?->isMidwife())
                <a href="{{ route('midwife.learning.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="border-radius:10px;">
                    <i class="bi bi-gear"></i> Manage Materials
                </a>
            @endif
        </div>
    @endif
</div>

{{-- Filter Toolbar --}}
<div class="card shadow-sm border mb-4" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('learning.index') }}" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label text-xs fw-700 text-muted mb-1 text-uppercase" style="letter-spacing:0.5px;">Search Topic / Keyword</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control form-control-sm bg-light border-start-0" 
                           placeholder="Search videos, articles, counseling guides..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label text-xs fw-700 text-muted mb-1 text-uppercase" style="letter-spacing:0.5px;">Format</label>
                <select name="type" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                    <option value="">All Formats</option>
                    <option value="video" {{ $activeFilter === 'video' ? 'selected' : '' }}>🎬 Playable Videos</option>
                    <option value="article" {{ $activeFilter === 'article' ? 'selected' : '' }}>📄 Articles &amp; Guides</option>
                    <option value="file" {{ $activeFilter === 'file' ? 'selected' : '' }}>📁 Downloadable Files</option>
                    <option value="link" {{ $activeFilter === 'link' ? 'selected' : '' }}>🔗 External Links</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label text-xs fw-700 text-muted mb-1 text-uppercase" style="letter-spacing:0.5px;">Category</label>
                <select name="category" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <option value="prenatal-care" {{ $activeCategory === 'prenatal-care' ? 'selected' : '' }}>🤰 Prenatal Care</option>
                    <option value="nutrition" {{ $activeCategory === 'nutrition' ? 'selected' : '' }}>🥗 Nutrition</option>
                    <option value="warning-signs" {{ $activeCategory === 'warning-signs' ? 'selected' : '' }}>⚠️ Warning Signs</option>
                    <option value="family-planning" {{ $activeCategory === 'family-planning' ? 'selected' : '' }}>👨‍👩‍👧 Family Planning</option>
                    <option value="postpartum" {{ $activeCategory === 'postpartum' ? 'selected' : '' }}>👶 Postpartum &amp; Newborn</option>
                    <option value="hcw-training" {{ $activeCategory === 'hcw-training' ? 'selected' : '' }}>🎓 HCW Training</option>
                </select>
            </div>
            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100" style="border-radius:8px;"><i class="bi bi-funnel-fill"></i></button>
                <a href="{{ route('learning.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="bi bi-x"></i></a>
            </div>
        </form>

        {{-- Interactive Category Chips --}}
        <div class="d-flex align-items-center gap-1.5 mt-3 pt-3 border-top flex-wrap" style="font-size:0.8rem;">
            <span class="text-xs text-muted fw-700 text-uppercase me-2" style="letter-spacing:0.5px;">Quick Filters:</span>
            <a href="{{ route('learning.index') }}" 
               class="badge text-decoration-none px-3 py-2 rounded-pill {{ !$activeFilter && !$activeCategory ? 'bg-primary text-white' : 'bg-light text-dark border' }}">
                All ({{ $materials->total() }})
            </a>
            <a href="{{ route('learning.index', ['type' => 'video']) }}" 
               class="badge text-decoration-none px-3 py-2 rounded-pill {{ $activeFilter === 'video' ? 'bg-danger text-white' : 'bg-light text-dark border' }}">
                <i class="bi bi-play-circle-fill me-1"></i> Playable Videos
            </a>
            <a href="{{ route('learning.index', ['category' => 'hcw-training']) }}" 
               class="badge text-decoration-none px-3 py-2 rounded-pill {{ $activeCategory === 'hcw-training' ? 'bg-primary text-white' : 'bg-light text-dark border' }}">
                <i class="bi bi-mortarboard-fill me-1"></i> HCW Training
            </a>
            <a href="{{ route('learning.index', ['category' => 'warning-signs']) }}" 
               class="badge text-decoration-none px-3 py-2 rounded-pill {{ $activeCategory === 'warning-signs' ? 'bg-warning text-dark' : 'bg-light text-dark border' }}">
                <i class="bi bi-exclamation-triangle me-1"></i> Warning Signs
            </a>
            <a href="{{ route('learning.index', ['category' => 'prenatal-care']) }}" 
               class="badge text-decoration-none px-3 py-2 rounded-pill {{ $activeCategory === 'prenatal-care' ? 'bg-primary text-white' : 'bg-light text-dark border' }}">
                🤰 Prenatal Care
            </a>
        </div>
    </div>
</div>

{{-- Media Grid --}}
<div class="row g-4">
    @forelse($materials as $material)
        @php
            $isVideo = $material->isPlayableVideo();
            $badgeColor = match($material->category) {
                'hcw-training' => '#6C5CE7',
                'warning-signs' => '#EF4444',
                'nutrition' => '#10B981',
                'family-planning' => '#F59E0B',
                default => '#0EA5E9',
            };
        @endphp
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border video-media-card position-relative" 
                 style="border-radius:18px; background:var(--bg-card); border-color:var(--border) !important; overflow:hidden; transition:transform 0.2s ease, box-shadow 0.2s ease;">
                
                {{-- Media Thumbnail with Video Overlay --}}
                <div class="position-relative overflow-hidden bg-dark" style="height:200px;">
                    @if($material->image_url)
                        <img src="{{ $material->image_url }}" alt="{{ $material->title }}" class="w-100 h-100" style="object-fit:cover; opacity:0.9;">
                    @else
                        <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white" 
                             style="background:linear-gradient(135deg, #1E1B4B, #312E81);">
                            <i class="bi {{ $isVideo ? 'bi-camera-video-fill' : 'bi-file-earmark-text' }}" style="font-size:2.8rem; opacity:0.6;"></i>
                        </div>
                    @endif

                    {{-- Play Icon Overlay for Videos --}}
                    @if($isVideo)
                        <a href="{{ route('learning.show', $material->id) }}" 
                           class="position-absolute top-50 start-50 translate-middle rounded-circle d-flex align-items-center justify-content-center text-white shadow"
                           style="width:52px; height:52px; background:rgba(239, 68, 68, 0.9); backdrop-filter:blur(4px); transition:transform 0.2s ease;">
                            <i class="bi bi-play-fill fs-3 ms-0.5"></i>
                        </a>
                        <span class="position-absolute bottom-0 start-0 m-2.5 badge bg-dark bg-opacity-75 text-white text-xs px-2 py-1">
                            <i class="bi bi-play-circle-fill text-danger me-1"></i> Stream Ready
                        </span>
                    @endif

                    {{-- Category Tag --}}
                    <span class="position-absolute top-0 end-0 m-2.5 badge text-white text-xs px-2.5 py-1" style="background:{{ $badgeColor }}; border-radius:8px;">
                        {{ ucfirst(str_replace('-', ' ', $material->category ?? 'General')) }}
                    </span>
                </div>

                {{-- Body Info --}}
                <div class="card-body p-3.5 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="fw-800 text-dark mb-1.5 line-clamp-2" style="font-size:0.98rem; line-height:1.4;">
                            {{ $material->title }}
                        </h6>
                        <p class="text-muted text-xs mb-3 line-clamp-2" style="line-height:1.5;">
                            {{ Str::limit(strip_tags($material->content), 120) }}
                        </p>
                    </div>

                    <div class="pt-2.5 border-top d-flex align-items-center justify-content-between">
                        <small class="text-muted text-xs">
                            <i class="bi bi-calendar3 me-1"></i>{{ $material->created_at->format('M d, Y') }}
                        </small>
                        <div class="d-flex gap-1">
                            <a href="{{ route('learning.show', $material->id) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1" style="border-radius:8px; font-size:0.8rem; font-weight:600;">
                                <i class="bi {{ $isVideo ? 'bi-play-fill' : 'bi-eye-fill' }}"></i> {{ $isVideo ? 'Play Video' : 'View Guide' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="card shadow-sm border p-5" style="border-radius:16px; background:var(--bg-card);">
                <i class="bi bi-camera-video text-muted mb-3" style="font-size:2.5rem;"></i>
                <h5 class="fw-700 text-dark">No Media Found</h5>
                <p class="text-muted text-xs mb-3">No learning materials match your active search or filter category.</p>
                <div>
                    <a href="{{ route('learning.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius:10px;">
                        Reset Filter
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($materials->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $materials->links() }}
    </div>
@endif

@push('styles')
<style>
    .video-media-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -4px rgba(108, 92, 231, 0.14) !important;
        border-color: var(--primary) !important;
    }
    .video-media-card:hover .rounded-circle {
        transform: scale(1.1);
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@endsection
