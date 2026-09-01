@extends('midwife.layout')

@section('title', 'Edit Learning Material - ReproCare')

@section('midwife-content')
<div style="width: 100%; max-width: 100%;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="bi bi-pencil-square"></i> Edit Learning Material</h1>
            <p class="text-muted mb-0">Update learning material information and content</p>
        </div>
        <a href="{{ route('midwife.learning.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Materials
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('midwife.learning.update', $material->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
        <!-- Left Column - Main Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Material Information</h5>
                </div>
                <div class="card-body">
                    <!-- Basic Information -->
                    <div class="mb-4">
                        <label for="title" class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" 
                               value="{{ old('title', $material->title) }}" required
                               placeholder="Enter material title">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="material_type" class="form-label fw-semibold">Material Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('material_type') is-invalid @enderror" id="material_type" name="material_type" required>
                            <option value="">Select Type</option>
                            <option value="article" {{ old('material_type', $material->material_type) == 'article' ? 'selected' : '' }}>Article</option>
                            <option value="link" {{ old('material_type', $material->material_type) == 'link' ? 'selected' : '' }}>External Link</option>
                            <option value="file" {{ old('material_type', $material->material_type) == 'file' ? 'selected' : '' }}>Downloadable File</option>
                        </select>
                        @error('material_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4" id="link_url_group" style="display: {{ old('material_type', $material->material_type) == 'link' ? 'block' : 'none' }};">
                        <label for="link_url" class="form-label fw-semibold">Link URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control @error('link_url') is-invalid @enderror" id="link_url" name="link_url" 
                               value="{{ old('link_url', $material->link_url) }}" placeholder="https://example.com"
                               {{ old('material_type', $material->material_type) == 'link' ? 'required' : '' }}>
                        <small class="text-muted">Enter the complete URL for external resources</small>
                        @error('link_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4" id="file_upload_group" style="display: {{ old('material_type', $material->material_type) == 'file' ? 'block' : 'none' }};">
                        <label for="file" class="form-label fw-semibold">Upload File</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file">
                        @if($material->file)
                            <div class="mt-2">
                                <small class="text-success"><i class="bi bi-check-circle"></i> Current file: {{ basename($material->file) }}</small>
                            </div>
                        @endif
                        <small class="text-muted">
                            Accepted: Images (JPG, PNG, GIF), Videos (MP4, AVI, MOV), Documents (PDF, DOC, XLS, PPT, TXT)<br>
                            Max size: 50MB. Upload new file to replace existing.
                        </small>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="content" class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="8" required
                                  placeholder="Enter material content...">{{ old('content', $material->content) }}</textarea>
                        <div class="form-text text-muted">
                            <strong>For articles:</strong> Full article content<br>
                            <strong>For links:</strong> Brief description of the resource<br>
                            <strong>For files:</strong> Description of what the file contains
                        </div>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('midwife.learning.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <a href="{{ route('learning.show', $material->id) }}" class="btn btn-outline-info" target="_blank">
                            <i class="bi bi-eye me-1"></i> View Material
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Update Material
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Sidebar -->
        <div class="col-lg-4">
            <!-- Thumbnail Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-image me-2"></i>Thumbnail</h5>
                </div>
                <div class="card-body">
                    <label class="form-label fw-semibold">Current Thumbnail</label>
                    @if($material->image_url)
                        <div class="mb-3">
                            <img src="{{ $material->image_url }}" alt="{{ $material->title }}"
                                 class="img-thumbnail w-100" style="height: 200px; object-fit: cover; border-radius: 8px;"
                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'text-center py-4 bg-light rounded\' style=\'border: 2px dashed #dee2e6; height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center;\'><i class=\'bi bi-image text-muted\' style=\'font-size: 2.5rem;\'></i><p class=\'text-muted mb-0 mt-2\'>Image not found</p></div>';">
                        </div>
                    @else
                        <div class="text-center py-4 mb-3 bg-light rounded" style="border: 2px dashed #dee2e6;">
                            <i class="bi bi-image text-muted" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mb-0 mt-2">No thumbnail uploaded</p>
                        </div>
                    @endif
                    
                    <label class="form-label fw-semibold">Upload New Thumbnail (Optional)</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" id="thumbnailInput" accept="image/*" onchange="previewThumbnail(event)">
                    <small class="form-text text-muted">Recommended: 800x600px, max 2MB</small>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    
                    <div id="thumbnailPreview" class="mt-3" style="display: none;">
                        <label class="form-label fw-semibold">New Thumbnail Preview</label>
                        <img id="thumbnailImage" src="" alt="Thumbnail Preview" class="img-thumbnail w-100" style="height: 200px; object-fit: cover; border-radius: 8px;">
                    </div>
                </div>
            </div>
            
            <!-- Material Info -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Material Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Created At</label>
                        <p class="mb-0 fw-semibold">{{ $material->created_at ? $material->created_at->format('F j, Y') : 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Last Updated</label>
                        <p class="mb-0 fw-semibold">{{ $material->updated_at ? $material->updated_at->format('F j, Y') : 'N/A' }}</p>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted">Current Type</label>
                        <p class="mb-0 fw-semibold">
                            <span class="badge bg-primary">{{ ucfirst($material->material_type ?? 'N/A') }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
document.getElementById('material_type').addEventListener('change', function() {
    var linkUrlGroup = document.getElementById('link_url_group');
    var fileUploadGroup = document.getElementById('file_upload_group');
    
    if (this.value === 'link') {
        linkUrlGroup.style.display = 'block';
        fileUploadGroup.style.display = 'none';
    } else if (this.value === 'file') {
        linkUrlGroup.style.display = 'none';
        fileUploadGroup.style.display = 'block';
    } else {
        linkUrlGroup.style.display = 'none';
        fileUploadGroup.style.display = 'none';
    }
});

function previewThumbnail(event) {
    const input = event.target;
    const preview = document.getElementById('thumbnailPreview');
    const image = document.getElementById('thumbnailImage');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            image.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endsection
