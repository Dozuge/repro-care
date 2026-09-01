@php
    $forumUser = auth()->user();
    $forumLayout = $forumUser?->role === 'bhw_president' ? 'bhw-president.layout' : 'user.layout';
    $forumSection = $forumUser?->role === 'bhw_president' ? 'bhw-president-content' : 'user-content';
@endphp

@extends($forumLayout)

@section('title', 'New Post - ReproCare Forum')

@section($forumSection)

@push('styles')
<style>
    .create-post-wrap { max-width: 680px; margin: 0 auto; }

    /* Upload drop zone */
    .image-drop-zone {
        background: var(--bg-input);
        border: 2px dashed var(--border);
        border-radius: 14px;
        padding: 1.75rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s ease, background 0.2s ease;
        position: relative;
    }
    .image-drop-zone:hover,
    .image-drop-zone.drag-over {
        border-color: var(--primary);
        background: var(--primary-subtle);
    }
    .image-drop-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .drop-zone-icon {
        font-size: 2.2rem;
        color: var(--text-muted);
        display: block;
        margin-bottom: 0.65rem;
        transition: color 0.2s ease;
    }
    .image-drop-zone:hover .drop-zone-icon { color: var(--primary-light); }
    .drop-zone-text { font-size: 0.875rem; color: var(--text-muted); }
    .drop-zone-sub  { font-size: 0.75rem; color: var(--text-muted); opacity: 0.7; margin-top: 0.25rem; }

    /* Image preview */
    .forum-image-preview {
        width: 100%;
        max-height: 300px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid var(--border);
    }

    /* Post textarea */
    .post-textarea {
        width: 100%;
        background: var(--bg-input);
        border: 1.5px solid var(--border);
        border-radius: 14px;
        color: var(--text);
        font-size: 0.95rem;
        line-height: 1.7;
        padding: 1rem 1.1rem;
        resize: vertical;
        min-height: 160px;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.3s ease;
    }
    .post-textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }
    .post-textarea::placeholder { color: var(--text-muted); opacity: 0.65; }

    /* Char counter */
    .char-counter { font-size: 0.78rem; color: var(--text-muted); text-align: right; margin-top: 0.3rem; }
    .char-counter.over { color: var(--danger); }

    /* Card header gradient */
    .create-post-header {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary), var(--accent-pink));
        padding: 1.25rem 1.5rem;
        border-radius: 18px 18px 0 0;
        color: #fff;
    }
    .create-post-header h5 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        font-size: 1.15rem;
        margin: 0;
    }
</style>
@endpush

@section('user-content')
@php($forumUser = auth()->user())

<div class="create-post-wrap fade-in-card">

    {{-- Back --}}
    <div class="mb-3">
        <a href="{{ route('forum.index') }}"
           style="color:var(--text-muted);font-size:0.875rem;text-decoration:none;display:inline-flex;align-items:center;gap:0.4rem;transition:color 0.2s ease;">
            <i class="bi bi-arrow-left"></i> Back to Forum
        </a>
    </div>

    <div class="card" style="border-radius:20px;overflow:hidden;">

        {{-- Header --}}
        <div class="create-post-header">
            <div style="position:relative;z-index:1;">
                <h5><i class="bi bi-chat-dots-fill me-2"></i>Create New Post</h5>
                <p style="margin:0.25rem 0 0;font-size:0.82rem;opacity:0.8;">
                    Share your experiences, questions, or support with the community
                </p>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body p-4">

            {{-- Flash --}}
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3 d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0 mt-1"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Author preview --}}
            <div class="d-flex align-items-center gap-3 mb-3 p-3"
                 style="background:var(--bg-card2);border:1px solid var(--border);border-radius:12px;">
                <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent-violet));display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:700;color:#fff;flex-shrink:0;box-shadow:0 2px 8px var(--primary-glow);">
                    {{ strtoupper(substr($forumUser->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight:700;font-size:0.9rem;color:var(--text);">{{ $forumUser->name }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">
                        Posting as
                        <span style="color:var(--primary-light);font-weight:600;">
                            {{ $forumUser->role === 'midwife' ? 'Midwife' : ($forumUser->role === 'bhw' ? 'BHW' : 'Patient') }}
                        </span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('forum.store') }}" enctype="multipart/form-data" id="createPostForm">
                @csrf

                {{-- Text content --}}
                <div class="mb-3">
                    <label for="content" class="form-label">
                        <i class="bi bi-pencil-fill me-1" style="color:var(--primary-light);"></i>
                        What's on your mind?
                    </label>
                    <textarea id="content"
                              name="content"
                              class="post-textarea"
                              placeholder="Share your thoughts, experiences, or questions with the community..."
                              maxlength="2000"
                              oninput="updateCharCount(this)"
                              required>{{ old('content') }}</textarea>
                    <div class="char-counter" id="charCounter">0 / 2000</div>
                </div>

                {{-- Image upload --}}
                <div class="mb-4">
                    <label class="form-label">
                        <i class="bi bi-image-fill me-1" style="color:var(--primary-light);"></i>
                        Add Image
                        <span style="color:var(--text-muted);font-weight:400;font-size:0.8rem;">(Optional)</span>
                    </label>

                    <div class="image-drop-zone" id="dropZone">
                        <input type="file"
                               id="post_image"
                               name="post_image"
                               accept="image/jpeg,image/jpg,image/png"
                               onchange="previewForumImage(this)">
                        <i class="bi bi-cloud-arrow-up drop-zone-icon"></i>
                        <div class="drop-zone-text">
                            <strong>Click to upload</strong> or drag & drop an image
                        </div>
                        <div class="drop-zone-sub">JPG, PNG up to 5MB</div>
                    </div>

                    {{-- Error --}}
                    @error('post_image')
                        <div style="font-size:0.82rem;color:var(--danger);margin-top:0.35rem;">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>{{ $message }}
                        </div>
                    @enderror

                    {{-- Preview --}}
                    <div id="imagePreviewContainer" style="display:none;margin-top:0.75rem;">
                        <img id="postImagePreview" src="" alt="Preview" class="forum-image-preview">
                        <button type="button"
                                class="btn btn-danger btn-sm mt-2"
                                onclick="removeForumImage()">
                            <i class="bi bi-trash me-1"></i> Remove Image
                        </button>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send-fill me-1"></i> Publish Post
                    </button>
                    <a href="{{ route('forum.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                </div>

            </form>
        </div>

        {{-- Footer tip --}}
        <div class="card-footer" style="padding:0.85rem 1.4rem;">
            <small style="color:var(--text-muted);font-size:0.78rem;display:flex;align-items:center;gap:0.4rem;">
                <i class="bi bi-shield-check-fill" style="color:var(--success);"></i>
                Be kind, be helpful, and respect everyone's privacy.
            </small>
        </div>

    </div>
</div>

@push('scripts')
<script>
function updateCharCount(el) {
    const count   = el.value.length;
    const counter = document.getElementById('charCounter');
    counter.textContent = count + ' / 2000';
    counter.classList.toggle('over', count >= 2000);
}

// Init on load
document.addEventListener('DOMContentLoaded', function () {
    const ta = document.getElementById('content');
    if (ta && ta.value.length > 0) updateCharCount(ta);
});

function previewForumImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('postImagePreview').src = e.target.result;
            document.getElementById('imagePreviewContainer').style.display = 'block';
            document.getElementById('dropZone').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeForumImage() {
    document.getElementById('post_image').value = '';
    document.getElementById('imagePreviewContainer').style.display = 'none';
    document.getElementById('postImagePreview').src = '';
    document.getElementById('dropZone').style.display = 'block';
}

// Drag-over effect on drop zone
const dz = document.getElementById('dropZone');
if (dz) {
    dz.addEventListener('dragover',  () => dz.classList.add('drag-over'));
    dz.addEventListener('dragleave', () => dz.classList.remove('drag-over'));
    dz.addEventListener('drop',      () => dz.classList.remove('drag-over'));
}
</script>
@endpush

@endsection
