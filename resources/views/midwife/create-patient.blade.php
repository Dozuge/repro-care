@extends('midwife.layout')

@section('title', 'Add New Woman - ReproCare')

@section('midwife-content')
<div class="py-4">
    <div class="page-hero fade-in-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
            <div>
                <h1 class="page-hero-title">
                    <i class="bi bi-person-plus-fill me-2"></i>Add New Woman
                </h1>
                <p class="page-hero-subtitle">Create a registered woman account and assign the correct purok.</p>
            </div>
            <a href="{{ route('midwife.patients') }}" class="btn-hero-primary">
                <i class="bi bi-arrow-left"></i> Back to Women
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card fade-in-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-person-vcard-fill me-2" style="color:var(--primary-light);"></i>Woman Information</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('midwife.patients.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="text-uppercase" style="letter-spacing:0.08em;color:var(--text-muted);font-size:0.78rem;">Personal Information</h6>
                    </div>

                    <div class="col-md-4">
                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="middle_initial" class="form-label">Middle Initial</label>
                        <input type="text" class="form-control @error('middle_initial') is-invalid @enderror" id="middle_initial" name="middle_initial" value="{{ old('middle_initial') }}" maxlength="2">
                        @error('middle_initial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-5">
                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="off">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="tel" class="form-control @error('contact_number') is-invalid @enderror" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" placeholder="09XX-XXX-XXXX">
                        @error('contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" max="{{ now()->subDay()->format('Y-m-d') }}" required>
                        @error('date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="text-uppercase" style="letter-spacing:0.08em;color:var(--text-muted);font-size:0.78rem;">Location & Account</h6>
                    </div>

                    <div class="col-md-6">
                        <label for="purok_id" class="form-label">Purok <span class="text-danger">*</span></label>
                        <select class="form-select @error('purok_id') is-invalid @enderror" id="purok_id" name="purok_id" required>
                            <option value="">Select purok</option>
                            @foreach($puroks as $purok)
                                <option value="{{ $purok->id }}" {{ (string) old('purok_id') === (string) $purok->id ? 'selected' : '' }}>
                                    {{ $purok->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('purok_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Barangay is set automatically to Burgos based on the selected purok.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Minimum 8 characters.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>

                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i>The woman will be created as an approved account and can sign in using the email and password entered here.
                </div>

                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <a href="{{ route('midwife.patients') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle-fill me-1"></i> Create Woman
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
