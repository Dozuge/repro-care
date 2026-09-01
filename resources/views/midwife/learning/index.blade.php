@extends('midwife.layout')

@section('title', 'Manage Learning Materials - ReproCare')

@section('midwife-content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-book"></i> Manage Learning Materials</h1>
        <a href="{{ route('midwife.learning.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Add Material
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('midwife.learning.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Search by title or content..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label for="type" class="form-label">Filter by Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <option value="article" {{ request('type') == 'article' ? 'selected' : '' }}>Articles</option>
                        <option value="link" {{ request('type') == 'link' ? 'selected' : '' }}>Links</option>
                        <option value="file" {{ request('type') == 'file' ? 'selected' : '' }}>Files</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
                @if(request('search') || request('type'))
                    <div class="col-12">
                        <a href="{{ route('midwife.learning.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="row">
        @if($materials->count() > 0)
            @foreach($materials as $material)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">{{ ucfirst($material->material_type) }}</span>
                            <form id="delete-form-{{ $material->id }}" action="{{ route('midwife.learning.destroy', $material->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                        <div class="card-body d-flex flex-column">
                            @if($material->image_url)
                                <div class="mb-3 rounded overflow-hidden" style="height: 200px; background: var(--bg-card2);">
                                    <img src="{{ $material->image_url }}"
                                         alt="{{ $material->title }}"
                                         class="img-fluid w-100 h-100"
                                         style="object-fit: cover;"
                                         onerror="this.style.display='none'; this.parentElement.querySelector('.img-placeholder').style.display='flex';">
                                    <div class="img-placeholder d-none align-items-center justify-content-center w-100 h-100 text-muted">
                                        <div class="text-center">
                                            <i class="bi bi-image" style="font-size: 2rem;"></i>
                                            <p class="mb-0 small">Image not available</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="mb-3 rounded d-flex align-items-center justify-content-center" style="height: 200px; background: var(--bg-card2); border: 2px dashed var(--border);">
                                    <div class="text-center text-muted">
                                        <i class="bi bi-image" style="font-size: 2.5rem;"></i>
                                        <p class="mb-0 small mt-2">No thumbnail</p>
                                    </div>
                                </div>
                            @endif
                            <h5 class="card-title">{{ $material->title }}</h5>
                            <p class="card-text">{{ \Illuminate\Support\Str::limit($material->content, 200) }}</p>

                            @if($material->material_type === 'link' && $material->link_url)
                                <div class="mb-3">
                                    <label class="form-label">Link Preview:</label>
                                    @if(strpos($material->link_url ?? '', 'youtube.com') !== false || strpos($material->link_url ?? '', 'youtu.be') !== false)
                                        <iframe src="{{ $material->link_url }}" class="embed-responsive embed-responsive-16by9" style="height: 315px;" allowfullscreen></iframe>
                                    @else
                                        <a href="{{ $material->link_url }}" target="_blank" class="btn btn-outline-primary">
                                            <i class="bi bi-box-arrow-up-right"></i> Open Link
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-auto">
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> Created: {{ $material->created_at->format('M j, Y g:i A') }}
                                </small>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end align-items-center">
                            <div class="d-flex gap-2">
                                <a href="{{ route('midwife.learning.show', $material->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('midwife.learning.edit', $material->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Are you sure you want to delete this material?')) { document.getElementById('delete-form-{{ $material->id }}').submit(); }">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
                    <h3 class="text-muted mt-3">No learning materials available</h3>
                    <p class="text-muted">Start by adding your first learning material.</p>
                    <a href="{{ route('midwife.learning.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Add First Material
                    </a>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Pagination -->
    @if($materials->count() > 0)
        <div class="d-flex justify-content-center mt-4">
            {{ $materials->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
