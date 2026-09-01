@extends('midwife.layout')

@section('title', 'Edit BHW President - ReproCare')

@section('midwife-content')

{{-- ═══════════════════════════════
     PAGE HERO
════════════════════════════════ --}}
<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-pencil-square me-2"></i>Edit BHW President
            </div>
            <p class="page-hero-subtitle">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, F j, Y') }}
                &nbsp;·&nbsp; Update BHW President information
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('midwife.bhw-presidents.show', $bhwPresident->id) }}" class="btn-hero-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Details
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

{{-- ═══════════════════════════════
     EDIT FORM
════════════════════════════════ --}}
<div class="card fade-in-card" style="border:none; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08);">
    <div class="card-body p-4">
        <form action="{{ route('midwife.bhw-presidents.update', $bhwPresident->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Personal Information -->
            <h6 class="mb-3" style="font-weight:800;"><i class="bi bi-person me-2"></i>Personal Information</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" id="first_name"
                           value="{{ old('first_name', $bhwPresident->first_name) }}"
                           class="form-control @error('first_name') is-invalid @enderror"
                           required autocomplete="off">
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">Middle Initial</label>
                    <input type="text" name="middle_initial" id="middle_initial"
                           value="{{ old('middle_initial', $bhwPresident->middle_initial) }}"
                           class="form-control @error('middle_initial') is-invalid @enderror"
                           maxlength="1" autocomplete="off">
                    @error('middle_initial')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" id="last_name"
                           value="{{ old('last_name', $bhwPresident->last_name) }}"
                           class="form-control @error('last_name') is-invalid @enderror"
                           required autocomplete="off">
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email"
                           value="{{ old('email', $bhwPresident->email) }}"
                           class="form-control @error('email') is-invalid @enderror"
                           required autocomplete="off">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                    <input type="tel" name="contact_number" id="contact_number"
                           value="{{ old('contact_number', $bhwPresident->contact_number) }}"
                           class="form-control @error('contact_number') is-invalid @enderror"
                           placeholder="09XX-XXX-XXXX" required autocomplete="off">
                    @error('contact_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" id="gender"
                            class="form-select @error('gender') is-invalid @enderror" required>
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $bhwPresident->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $bhwPresident->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" name="date_of_birth" id="date_of_birth"
                           value="{{ old('date_of_birth', $bhwPresident->date_of_birth) }}"
                           class="form-control @error('date_of_birth') is-invalid @enderror"
                           required max="{{ date('Y-m-d') }}" autocomplete="off">
                    @error('date_of_birth')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Profile Image</label>
                    <input type="file" name="profile_image" id="profile_image"
                           class="form-control @error('profile_image') is-invalid @enderror"
                           accept="image/*">
                    @if($bhwPresident->profile_image)
                        <small class="text-muted">Current image will be replaced if you upload a new one.</small>
                    @endif
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
                    <input type="text" id="barangay"
                           value="Barangay Burgos, San Carlos City, Pangasinan"
                           class="form-control"
                           readonly>
                    <small class="text-muted">Barangay is automatically set to Barangay Burgos, San Carlos City, Pangasinan.</small>
                </div>
            </div>

            <!-- Password Change (Optional) -->
            <h6 class="mb-3" style="font-weight:800;"><i class="bi bi-shield-lock me-2"></i>Change Password (Optional)</h6>
            <p class="text-muted mb-3">Leave blank if you don't want to change the password.</p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-control" autocomplete="new-password">
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-between pt-3" style="border-top:1px solid var(--border);">
                <a href="{{ route('midwife.bhw-presidents.show', $bhwPresident->id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Update BHW President
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
