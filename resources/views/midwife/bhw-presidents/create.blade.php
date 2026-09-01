@extends('midwife.layout')

@section('title', isset($currentPresident) && $currentPresident ? 'Change BHW President - ReproCare' : 'Add BHW President - ReproCare')

@push('scripts')
<script>
    // Prevent browser autofill
    window.addEventListener('load', function() {
        setTimeout(function() {
            const fields = ['first_name', 'middle_initial', 'last_name', 'email', 'contact_number', 'date_of_birth', 'password', 'password_confirmation'];
            fields.forEach(function(id) {
                const el = document.getElementById(id);
                if (el && el.value && !el.dataset.userEntered) el.value = '';
            });
        }, 100);
    });
</script>
@endpush

@section('midwife-content')

{{-- ═══════════════════════════════
     PAGE HERO
════════════════════════════════ --}}
<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-person-plus-fill me-2"></i>{{ isset($currentPresident) && $currentPresident ? 'Change BHW President' : 'Add BHW President' }}
            </div>
            <p class="page-hero-subtitle">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, F j, Y') }}
                &nbsp;·&nbsp; {{ isset($currentPresident) && $currentPresident ? 'Replace the current BHW President with a new one' : 'Create a new BHW President account' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('midwife.bhw-presidents.index') }}" class="btn-hero-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to BHW Presidents
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" 
         style="background:rgba(25,135,84,0.1); border:1px solid rgba(25,135,84,0.3); color:var(--success); border-radius:10px;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter:invert(1);"></button>
    </div>
@endif

@if(isset($currentPresident) && $currentPresident)
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert" 
         style="background:rgba(255,193,7,0.1); border:1px solid rgba(255,193,7,0.3); color:var(--warning); border-radius:10px;">
        <i class="bi bi-info-circle-fill me-2"></i>
        You are about to change the current BHW President ({{ $currentPresident->name }}). The previous president will be archived.
        <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter:invert(1);"></button>
    </div>
@endif

{{-- ═══════════════════════════════
     CREATE FORM
════════════════════════════════ --}}
<div class="card fade-in-card" style="border:none; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08);">
    <div class="card-body p-4">
        <form action="{{ route('midwife.bhw-presidents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Personal Information -->
            <h6 class="mb-3" style="font-weight:800;"><i class="bi bi-person me-2"></i>Personal Information</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                           id="first_name" name="first_name" value="{{ old('first_name') }}" required 
                           autocomplete="off" readonly onfocus="this.removeAttribute('readonly')">
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">Middle Initial</label>
                    <input type="text" class="form-control @error('middle_initial') is-invalid @enderror" 
                           id="middle_initial" name="middle_initial" value="{{ old('middle_initial') }}"
                           maxlength="1" autocomplete="off" readonly onfocus="this.removeAttribute('readonly')">
                    @error('middle_initial')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                           id="last_name" name="last_name" value="{{ old('last_name') }}" required 
                           autocomplete="off" readonly onfocus="this.removeAttribute('readonly')">
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" required
                           autocomplete="off" readonly onfocus="this.removeAttribute('readonly')">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control @error('contact_number') is-invalid @enderror" 
                           id="contact_number" name="contact_number" value="{{ old('contact_number') }}"
                           placeholder="09XX-XXX-XXXX" required autocomplete="off">
                    @error('contact_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select class="form-select @error('gender') is-invalid @enderror" 
                            id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                           id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}"
                           max="{{ date('Y-m-d') }}" required autocomplete="off">
                    @error('date_of_birth')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Profile Image</label>
                    <input type="file" class="form-control @error('profile_image') is-invalid @enderror" 
                           id="profile_image" name="profile_image" accept="image/*">
                    @error('profile_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Barangay Information -->
            <h6 class="mb-3" style="font-weight:800;"><i class="bi bi-geo-alt me-2"></i>Barangay Information</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Barangay</label>
                    <input type="text" class="form-control" id="barangay" value="Barangay Burgos, San Carlos City, Pangasinan" readonly>
                    <small class="text-muted">Barangay is automatically set to Barangay Burgos, San Carlos City, Pangasinan.</small>
                </div>
            </div>

            <!-- Account Credentials -->
            <h6 class="mb-3" style="font-weight:800;"><i class="bi bi-shield-lock me-2"></i>Account Credentials</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" required
                           autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly')">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Minimum 8 characters</small>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" 
                           id="password_confirmation" name="password_confirmation" required
                           autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly')">
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-between pt-3" style="border-top:1px solid var(--border);">
                <a href="{{ route('midwife.bhw-presidents.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </a>
                <div class="d-flex gap-2">
                    <button type="reset" class="btn btn-outline-warning">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> {{ isset($currentPresident) && $currentPresident ? 'Change BHW President' : 'Create BHW President' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
