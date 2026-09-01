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

    $articlesCount = $materials->where('material_type', 'article')->count();
    $linksCount = $materials->where('material_type', 'link')->count();
    $filesCount = $materials->where('material_type', 'file')->count();
    $activeFilter = request('type');
@endphp

@extends($learningLayout)

@section('title', 'Learning Materials - ReproCare')

@section($learningSection)
<div class="workspace-stack">
    <div class="page-hero fade-in-card">
        <div class="workspace-toolbar" style="position:relative;z-index:1;">
            <div>
                <div class="page-hero-title">
                    <i class="bi bi-journal-richtext me-2"></i>Learning Materials
                </div>
                <p class="page-hero-subtitle">Trusted articles, files, and links to support every stage of reproductive care.</p>
            </div>
            @if($learningUser?->isMidwife())
                <div class="workspace-toolbar-actions">
                    <a href="{{ route('midwife.learning.create') }}" class="btn-hero-primary">
                        <i class="bi bi-plus-circle-fill"></i>Add Material
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="metric-grid">
        <div class="metric-card metric-card-primary fade-in-card">
            <i class="bi bi-file-text metric-card-icon"></i>
            <div class="metric-card-label">Articles</div>
            <div class="metric-card-value">{{ $articlesCount }}</div>
            <div class="metric-card-note">Written guides you can review at your own pace.</div>
        </div>
        <div class="metric-card metric-card-green fade-in-card">
            <i class="bi bi-link-45deg metric-card-icon"></i>
            <div class="metric-card-label">Links</div>
            <div class="metric-card-value">{{ $linksCount }}</div>
            <div class="metric-card-note">External references from helpful reproductive health resources.</div>
        </div>
        <div class="metric-card metric-card-amber fade-in-card">
            <i class="bi bi-file-earmark-arrow-down metric-card-icon"></i>
            <div class="metric-card-label">Files</div>
            <div class="metric-card-value">{{ $filesCount }}</div>
            <div class="metric-card-note">Downloadable materials for offline reading and sharing.</div>
        </div>
        <div class="metric-card metric-card-indigo fade-in-card">
            <i class="bi bi-search-heart metric-card-icon"></i>
            <div class="metric-card-label">Visible Results</div>
            <div class="metric-card-value">{{ $materials->total() }}</div>
            <div class="metric-card-note">{{ request('search') ? 'Search results for your current query.' : 'All available materials in the current filter.' }}</div>
        </div>
    </div>

    <div class="workspace-panel fade-in-card">
        <div class="workspace-panel-header">
            <h2 class="workspace-panel-title"><i class="bi bi-sliders"></i>Browse Materials</h2>
            <p class="workspace-panel-subtitle">Filter by content type or search by topic.</p>
        </div>
        <div class="workspace-panel-body">
            <form method="GET" action="{{ route('learning.index') }}" class="workspace-filter-grid">
                <div class="span-5">
                    <label class="form-label">Search Topic</label>
                    <input type="text" name="search" class="form-control" placeholder="Search materials..." value="{{ request('search') }}">
                </div>
                <div class="span-4">
                    <label class="form-label">Material Type</label>
                    <select name="type" class="form-select">
                        <option value="">All materials</option>
                        <option value="article" {{ $activeFilter === 'article' ? 'selected' : '' }}>Articles</option>
                        <option value="link" {{ $activeFilter === 'link' ? 'selected' : '' }}>Links</option>
                        <option value="file" {{ $activeFilter === 'file' ? 'selected' : '' }}>Files</option>
                    </select>
                </div>
                <div class="span-3 workspace-filter-actions">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                    <a href="{{ route('learning.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i>Reset
                    </a>
                </div>
            </form>

            <div class="section-chip-row mt-4">
                <a href="{{ route('learning.index') }}" class="summary-chip {{ !$activeFilter ? 'chip-primary' : 'chip-info' }}">All Materials</a>
                <a href="{{ route('learning.index', ['type' => 'article']) }}" class="summary-chip {{ $activeFilter === 'article' ? 'chip-primary' : 'chip-info' }}">Articles</a>
                <a href="{{ route('learning.index', ['type' => 'link']) }}" class="summary-chip {{ $activeFilter === 'link' ? 'chip-primary' : 'chip-info' }}">Links</a>
                <a href="{{ route('learning.index', ['type' => 'file']) }}" class="summary-chip {{ $activeFilter === 'file' ? 'chip-primary' : 'chip-info' }}">Files</a>
            </div>
        </div>
    </div>

    @if($materials->count() > 0)
        <div class="row g-4">
            @foreach($materials as $material)
                @php
                    $typeMap = [
                        'article' => ['chip' => 'chip-primary', 'icon' => 'bi-file-text', 'cta' => 'Read Material'],
                        'link' => ['chip' => 'chip-success', 'icon' => 'bi-link-45deg', 'cta' => 'Open Link'],
                        'file' => ['chip' => 'chip-warning', 'icon' => 'bi-file-earmark-arrow-down', 'cta' => 'View Material'],
                    ];
                    $type = $typeMap[$material->material_type] ?? $typeMap['article'];
                @endphp
                <div class="col-xl-4 col-md-6">
                    <div class="workspace-panel h-100 fade-in-card">
                        <div class="workspace-panel-body h-100 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <span class="summary-chip {{ $type['chip'] }}">
                                    <i class="bi {{ $type['icon'] }}"></i>{{ ucfirst($material->material_type) }}
                                </span>
                                @if($learningUser?->isMidwife())
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('midwife.learning.edit', $material->id) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                            <li>
                                                <form action="{{ route('midwife.learning.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Delete this material?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            @if($material->image_url)
                                <div class="mb-3 rounded-4 overflow-hidden border" style="border-color:var(--border)!important;height:190px;">
                                    <img src="{{ $material->image_url }}" alt="{{ $material->title }}" class="w-100 h-100" style="object-fit:cover;">
                                </div>
                            @endif

                            <h3 class="h5 mb-2">{{ $material->title }}</h3>
                            <p class="text-muted mb-3 flex-grow-1">{{ \Illuminate\Support\Str::limit(strip_tags($material->content), 150) }}</p>

                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>Published {{ $material->created_at->format('M j, Y') }}
                                </small>
                            </div>

                            @if($material->material_type === 'link' && $material->link_url)
                                <a href="{{ $material->link_url }}" target="_blank" class="btn btn-primary w-100">
                                    <i class="bi bi-box-arrow-up-right me-2"></i>{{ $type['cta'] }}
                                </a>
                            @else
                                <a href="{{ route('learning.show', $material->id) }}" class="btn btn-primary w-100">
                                    <i class="bi bi-eye me-2"></i>{{ $type['cta'] }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center">
            {{ $materials->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="workspace-panel fade-in-card">
            <div class="empty-state-panel">
                <i class="bi bi-journal-x"></i>
                <h3>No learning materials found</h3>
                <p>{{ request('search') ? 'Try a broader topic or remove one of the filters.' : 'There are no learning materials available in this category yet.' }}</p>
                @if(request('search') || request('type'))
                    <a href="{{ route('learning.index') }}" class="btn btn-primary mt-3">View All Materials</a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
