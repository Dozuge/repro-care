@extends('midwife.layout')

@section('title', 'Add Learning Material - ReproCare')

@section('midwife-content')
<div class="py-4" style="width: 100%; max-width: 100%;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="bi bi-plus-square"></i> Add Learning Material</h1>
            <p class="text-muted mb-0">Create a new learning material for patients</p>
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

    <form method="POST" action="{{ route('midwife.learning.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4">
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
                                   value="{{ old('title') }}" required
                                   placeholder="Enter material title">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="material_type" class="form-label fw-semibold">Material Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('material_type') is-invalid @enderror" id="material_type" name="material_type" required onchange="if(this.value === 'file') { document.getElementById('file_upload_group').style.display = 'block'; document.getElementById('file').setAttribute('required', 'required'); } else { document.getElementById('file_upload_group').style.display = 'none'; document.getElementById('file').removeAttribute('required'); } if(this.value === 'link') { document.getElementById('link_url_group').style.display = 'block'; document.getElementById('link_url').setAttribute('required', 'required'); } else { document.getElementById('link_url_group').style.display = 'none'; document.getElementById('link_url').removeAttribute('required'); }">
                                <option value="">Select Type</option>
                                <option value="article" {{ old('material_type') == 'article' ? 'selected' : '' }}>Article</option>
                                <option value="link" {{ old('material_type') == 'link' ? 'selected' : '' }}>External Link</option>
                                <option value="file" {{ old('material_type') == 'file' ? 'selected' : '' }}>Downloadable File</option>
                            </select>
                            @error('material_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4" id="link_url_group" style="display: {{ old('material_type') == 'link' ? 'block' : 'none' }};">
                            <label for="link_url" class="form-label fw-semibold">Link URL <span class="text-danger">*</span></label>
                            <input type="url" class="form-control @error('link_url') is-invalid @enderror" id="link_url" name="link_url" 
                                   value="{{ old('link_url') }}" placeholder="https://example.com" {{ old('material_type') == 'link' ? 'required' : '' }}>
                            <small class="text-muted">Enter the complete URL for external resources</small>
                            @error('link_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4" id="file_upload_group" style="display: {{ old('material_type') == 'file' ? 'block' : 'none' }};">
                            <label for="file" class="form-label fw-semibold">Upload File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" {{ old('material_type') == 'file' ? 'required' : '' }}>
                            <small class="text-muted">
                                Accepted: Images (JPG, PNG, GIF), Videos (MP4, AVI, MOV), Documents (PDF, DOC, XLS, PPT, TXT)<br>
                                Max size: 100MB
                            </small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="content" class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="8" required
                                      placeholder="Enter material content...">{{ old('content') }}</textarea>
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
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Save Material
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
                        <label class="form-label fw-semibold">Upload Thumbnail (Optional)</label>
                        <div class="text-center py-4 mb-3 bg-light rounded" style="border: 2px dashed #dee2e6;">
                            <i class="bi bi-image text-muted" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mb-0 mt-2">No thumbnail uploaded</p>
                        </div>
                        
                        <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" id="thumbnailInput" accept="image/*" onchange="previewThumbnail(event)">
                        <small class="form-text text-muted">Recommended: 800x600px, max 2MB</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <div id="thumbnailPreview" class="mt-3" style="display: none;">
                            <label class="form-label fw-semibold">Thumbnail Preview</label>
                            <img id="thumbnailImage" src="" alt="Thumbnail Preview" class="img-thumbnail w-100" style="height: 200px; object-fit: cover; border-radius: 8px;">
                        </div>
                    </div>
                </div>
                
                <!-- Tips -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Tips</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="mb-2 fw-semibold">Best Practices:</p>
                            <ul class="mb-0 text-muted small">
                                <li>Use clear, descriptive titles</li>
                                <li>Include relevant keywords in content</li>
                                <li>Add a thumbnail to increase engagement</li>
                                <li>For links, provide a brief description</li>
                                <li>For files, describe what users will learn</li>
                            </ul>
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
function toggleMaterialTypeFields() {
    var materialType = document.getElementById('material_type').value;
    var linkUrlGroup = document.getElementById('link_url_group');
    var fileUploadGroup = document.getElementById('file_upload_group');
    
    if (materialType === 'link') {
        linkUrlGroup.style.display = 'block';
        document.getElementById('link_url').setAttribute('required', 'required');
        if (fileUploadGroup) {
            fileUploadGroup.style.display = 'none';
            document.getElementById('file').removeAttribute('required');
        }
    } else if (materialType === 'file') {
        linkUrlGroup.style.display = 'none';
        document.getElementById('link_url').removeAttribute('required');
        if (fileUploadGroup) {
            fileUploadGroup.style.display = 'block';
            document.getElementById('file').setAttribute('required', 'required');
        }
    } else {
        linkUrlGroup.style.display = 'none';
        document.getElementById('link_url').removeAttribute('required');
        if (fileUploadGroup) {
            fileUploadGroup.style.display = 'none';
            document.getElementById('file').removeAttribute('required');
        }
    }
}

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

// Run immediately (script is at bottom of page)
toggleMaterialTypeFields();

// Run on change
document.getElementById('material_type').addEventListener('change', toggleMaterialTypeFields);
</script>
@endsection
