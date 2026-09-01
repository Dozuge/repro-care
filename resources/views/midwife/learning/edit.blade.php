@php
    $layout = auth()->user()?->isCho() ? 'cho.layout' : 'midwife.layout';
    $section = auth()->user()?->isCho() ? 'cho-content' : 'midwife-content';
@endphp

@extends($layout)

@section('title', 'Edit Media & Learning Material - ReproCare')

@section($section)
<div class="py-3" style="width: 100%; max-width: 100%;">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-800 text-dark mb-1" style="font-family:'Plus Jakarta Sans',sans-serif;">
                <i class="bi bi-pencil-square text-primary me-2"></i>Edit Media &amp; Learning Material
            </h2>
            <p class="text-muted mb-0" style="font-size:0.9rem;">Update video links, uploaded files, or clinical counseling points.</p>
        </div>
        <a href="{{ route('midwife.learning.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1" style="border-radius:10px;">
            <i class="bi bi-arrow-left"></i> Back to Media Library
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius:14px;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius:14px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Please correct the following:</strong>
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
        
        <div class="row g-4">
            <!-- Left Column - Main Details -->
            <div class="col-lg-8">
                <div class="card shadow-sm border mb-4" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h6 class="fw-800 mb-0 text-dark"><i class="bi bi-info-circle-fill me-2 text-primary"></i>Media Content Information</h6>
                    </div>
                    <div class="card-body p-4">
                        {{-- Title --}}
                        <div class="mb-3">
                            <label for="title" class="form-label fw-700 text-xs text-uppercase text-muted" style="letter-spacing:0.5px;">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" 
                                   value="{{ old('title', $material->title) }}" required style="border-radius:10px;">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Material Type --}}
                        <div class="mb-3">
                            <label for="material_type" class="form-label fw-700 text-xs text-uppercase text-muted" style="letter-spacing:0.5px;">Material Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('material_type') is-invalid @enderror" id="material_type" name="material_type" required onchange="handleTypeChange(this.value)" style="border-radius:10px;">
                                <option value="video" {{ old('material_type', $material->material_type) === 'video' ? 'selected' : '' }}>🎬 Playable Video (Upload MP4 or Stream URL)</option>
                                <option value="article" {{ old('material_type', $material->material_type) === 'article' ? 'selected' : '' }}>📄 Educational Article</option>
                                <option value="link" {{ old('material_type', $material->material_type) === 'link' ? 'selected' : '' }}>🔗 External Resource Link</option>
                                <option value="file" {{ old('material_type', $material->material_type) === 'file' ? 'selected' : '' }}>📁 Downloadable Clinical Document (PDF / PPT / Doc)</option>
                            </select>
                        </div>

                        {{-- Video URL / Stream Input --}}
                        <div class="mb-3" id="video_url_group" style="display: {{ old('material_type', $material->material_type) === 'video' ? 'block' : 'none' }};">
                            <label for="video_url" class="form-label fw-700 text-xs text-uppercase text-muted" style="letter-spacing:0.5px;">
                                Video Streaming URL (YouTube / Vimeo / Direct Stream)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-youtube text-danger"></i></span>
                                <input type="url" class="form-control" id="video_url" name="video_url" 
                                       value="{{ old('video_url', $material->video_url) }}" placeholder="https://www.youtube.com/watch?v=... or Vimeo link" style="border-radius:0 10px 10px 0;">
                            </div>
                        </div>

                        {{-- MP4 Video / File Upload --}}
                        <div class="mb-3" id="file_upload_group" style="display: {{ in_array(old('material_type', $material->material_type), ['video', 'file']) ? 'block' : 'none' }};">
                            <label for="file" class="form-label fw-700 text-xs text-uppercase text-muted" style="letter-spacing:0.5px;">
                                Upload / Replace File (MP4, WEBM, MOV, PDF, DOCX)
                            </label>
                            <input type="file" class="form-control" id="file" name="file" accept=".mp4,.webm,.mov,.avi,.pdf,.doc,.docx,.ppt,.pptx" style="border-radius:10px;">
                            @if($material->file)
                                <div class="mt-2 text-xs text-success fw-600">
                                    <i class="bi bi-check-circle-fill me-1"></i> Current file: {{ basename($material->file) }}
                                </div>
                            @endif
                        </div>

                        {{-- External Link URL --}}
                        <div class="mb-3" id="link_url_group" style="display: {{ old('material_type', $material->material_type) === 'link' ? 'block' : 'none' }};">
                            <label for="link_url" class="form-label fw-700 text-xs text-uppercase text-muted" style="letter-spacing:0.5px;">External Resource URL</label>
                            <input type="url" class="form-control" id="link_url" name="link_url" value="{{ old('link_url', $material->link_url) }}" style="border-radius:10px;">
                        </div>
                        
                        {{-- Content / Description --}}
                        <div class="mb-3">
                            <label for="content" class="form-label fw-700 text-xs text-uppercase text-muted" style="letter-spacing:0.5px;">Description &amp; Key Teaching Points <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="6" required style="border-radius:10px;">{{ old('content', $material->content) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Categories & Thumbnail -->
            <div class="col-lg-4">
                <div class="card shadow-sm border mb-4" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h6 class="fw-800 mb-0 text-dark"><i class="bi bi-tags-fill me-2 text-primary"></i>Category &amp; Target</h6>
                    </div>
                    <div class="card-body p-4">
                        {{-- Category --}}
                        <div class="mb-3">
                            <label for="category" class="form-label fw-700 text-xs text-uppercase text-muted" style="letter-spacing:0.5px;">Topic Category</label>
                            <select class="form-select" id="category" name="category" style="border-radius:10px;">
                                <option value="prenatal-care" {{ old('category', $material->category) === 'prenatal-care' ? 'selected' : '' }}>🤰 Prenatal Care</option>
                                <option value="nutrition" {{ old('category', $material->category) === 'nutrition' ? 'selected' : '' }}>🥗 Maternal Nutrition</option>
                                <option value="warning-signs" {{ old('category', $material->category) === 'warning-signs' ? 'selected' : '' }}>⚠️ Warning Signs &amp; Preeclampsia</option>
                                <option value="family-planning" {{ old('category', $material->category) === 'family-planning' ? 'selected' : '' }}>👨‍👩‍👧 Family Planning &amp; Contraception</option>
                                <option value="postpartum" {{ old('category', $material->category) === 'postpartum' ? 'selected' : '' }}>👶 Postpartum &amp; Newborn Care</option>
                                <option value="hcw-training" {{ old('category', $material->category) === 'hcw-training' ? 'selected' : '' }}>🎓 Healthcare Worker (HCW) Training</option>
                                <option value="general" {{ old('category', $material->category) === 'general' ? 'selected' : '' }}>General Health Education</option>
                            </select>
                        </div>

                        {{-- Week Number (Optional for Pregnancy Guide) --}}
                        <div class="mb-3">
                            <label for="week_number" class="form-label fw-700 text-xs text-uppercase text-muted" style="letter-spacing:0.5px;">Pregnancy Week # (Optional)</label>
                            <input type="number" class="form-control" id="week_number" name="week_number" value="{{ old('week_number', $material->week_number) }}" min="1" max="42" style="border-radius:10px;">
                        </div>

                        {{-- Cover Image Thumbnail --}}
                        <div class="mb-4">
                            <label for="image" class="form-label fw-700 text-xs text-uppercase text-muted" style="letter-spacing:0.5px;">Cover Thumbnail Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" style="border-radius:10px;">
                            @if($material->image_url)
                                <div class="mt-2 rounded-3 overflow-hidden border" style="height:100px;">
                                    <img src="{{ $material->image_url }}" alt="Cover" class="w-100 h-100" style="object-fit:cover;">
                                </div>
                            @endif
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary fw-700 py-2.5" style="border-radius:10px;">
                                <i class="bi bi-save me-1"></i> Update Media Material
                            </button>
                            <a href="{{ route('midwife.learning.index') }}" class="btn btn-light border py-2" style="border-radius:10px;">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function handleTypeChange(val) {
    const videoGrp = document.getElementById('video_url_group');
    const fileGrp = document.getElementById('file_upload_group');
    const linkGrp = document.getElementById('link_url_group');

    if (val === 'video') {
        videoGrp.style.display = 'block';
        fileGrp.style.display = 'block';
        linkGrp.style.display = 'none';
    } else if (val === 'link') {
        videoGrp.style.display = 'none';
        fileGrp.style.display = 'none';
        linkGrp.style.display = 'block';
    } else if (val === 'file') {
        videoGrp.style.display = 'none';
        fileGrp.style.display = 'block';
        linkGrp.style.display = 'none';
    } else {
        videoGrp.style.display = 'none';
        fileGrp.style.display = 'none';
        linkGrp.style.display = 'none';
    }
}
</script>
@endpush

@endsection
