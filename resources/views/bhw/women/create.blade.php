@extends('bhw.layout')

@section('title', 'Create Woman - BHW Portal | ReproCare')

@section('bhw-content')
<div class="py-4">
    <div class="page-hero fade-in-card mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3" style="position:relative;z-index:1;">
            <div>
                <div class="page-hero-title">
                    <i class="bi bi-person-plus-fill me-2"></i>Add Woman
                </div>
                <p class="page-hero-subtitle">Create a registered account or save an unregistered woman record for follow-up care.</p>
            </div>
            <a href="{{ route('bhw.patients') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i> Back to Women
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4 fade-in-card">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('bhw.patients.store') }}" class="fade-in-card">
        @csrf

        <div class="card mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <input type="radio" class="btn-check" name="patient_type" id="patient_registered" value="registered" {{ old('patient_type', 'registered') === 'registered' ? 'checked' : '' }} onchange="togglePatientType()">
                    <label class="btn btn-outline-primary" for="patient_registered">
                        <i class="bi bi-person-badge me-1"></i> Registered
                    </label>

                    <input type="radio" class="btn-check" name="patient_type" id="patient_unregistered" value="unregistered" {{ old('patient_type') === 'unregistered' ? 'checked' : '' }} onchange="togglePatientType()">
                    <label class="btn btn-outline-primary" for="patient_unregistered">
                        <i class="bi bi-person-plus me-1"></i> Unregistered
                    </label>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Middle Initial</label>
                        <input type="text" name="middle_initial" class="form-control @error('middle_initial') is-invalid @enderror" value="{{ old('middle_initial') }}" maxlength="10">
                        @error('middle_initial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Last Name *</label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}">
                        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}">
                        @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Barangay</label>
                        <input type="text" class="form-control" value="Barangay Burgos Padlan, San Carlos City, Pangasinan" readonly>
                        <input type="hidden" name="barangay" value="Barangay Burgos Padlan, San Carlos City, Pangasinan">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Purok</label>
                        <select name="purok_id" class="form-select @error('purok_id') is-invalid @enderror">
                            <option value="">Select purok</option>
                            @foreach($puroks as $purok)
                                <option value="{{ $purok->id }}" {{ old('purok_id') == $purok->id ? 'selected' : '' }}>{{ $purok->name }}</option>
                            @endforeach
                        </select>
                        @error('purok_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-heart-pulse-fill me-2 text-danger"></i>Pregnancy Details</h5>
            </div>
            <div class="card-body p-4">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_pregnant" name="is_pregnant" {{ old('is_pregnant') ? 'checked' : '' }} onchange="togglePregnancyFields()">
                    <label class="form-check-label fw-semibold" for="is_pregnant">This woman is currently pregnant</label>
                </div>

                <div id="pregnancy-fields" class="d-none">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Last Menstrual Period (LMP) *</label>
                            <input type="date" name="lmp" class="form-control @error('lmp') is-invalid @enderror" value="{{ old('lmp') }}">
                            @error('lmp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expected Due Date (EDD)</label>
                            <input type="date" name="edd" class="form-control @error('edd') is-invalid @enderror" value="{{ old('edd') }}">
                            @error('edd')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gravida</label>
                            <input type="number" name="gravida" class="form-control @error('gravida') is-invalid @enderror" value="{{ old('gravida', 1) }}" min="1">
                            @error('gravida')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Para</label>
                            <input type="number" name="para" class="form-control @error('para') is-invalid @enderror" value="{{ old('para', 0) }}" min="0">
                            @error('para')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">GTPAL</label>
                            <input type="text" name="gtpal" class="form-control @error('gtpal') is-invalid @enderror" value="{{ old('gtpal') }}" placeholder="G-T-P-A-L">
                            @error('gtpal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Obstetric History</label>
                            <textarea name="obstetric_history" class="form-control @error('obstetric_history') is-invalid @enderror" rows="3" placeholder="Previous complications, delivery notes, or follow-up reminders">{{ old('obstetric_history') }}</textarea>
                            @error('obstetric_history')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="registered-fields" class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-shield-lock-fill me-2 text-primary"></i>Account Access</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
                <div class="alert alert-info mt-3 mb-0">
                    Registered women will wait for approval before they can sign in.
                </div>
            </div>
        </div>

        <div id="unregistered-fields" class="card mb-4 d-none">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-journal-medical me-2 text-warning"></i>Visit Information</h5>
            </div>
            <div class="card-body p-4">
                <label class="form-label">Reason for Visit *</label>
                <textarea name="reason_for_visit" class="form-control @error('reason_for_visit') is-invalid @enderror" rows="3" placeholder="Describe why the patient was recorded as a walk-in">{{ old('reason_for_visit') }}</textarea>
                @error('reason_for_visit')<div class="invalid-feedback">{{ $message }}</div>@enderror

                <div class="alert alert-warning mt-3 mb-0">
                    Unregistered women can still be tracked here and converted into full accounts later.
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle-fill me-1"></i> Save Woman
            </button>
            <a href="{{ route('bhw.patients') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
function togglePatientType() {
    const registered = document.getElementById('patient_registered').checked;
    const registeredFields = document.getElementById('registered-fields');
    const unregisteredFields = document.getElementById('unregistered-fields');
    const emailInput = document.querySelector('input[name="email"]');
    const passwordInput = document.querySelector('input[name="password"]');
    const passwordConfirmInput = document.querySelector('input[name="password_confirmation"]');
    const reasonInput = document.querySelector('textarea[name="reason_for_visit"]');

    registeredFields.classList.toggle('d-none', !registered);
    unregisteredFields.classList.toggle('d-none', registered);

    emailInput.required = registered;
    passwordInput.required = registered;
    passwordConfirmInput.required = registered;
    reasonInput.required = !registered;
}

function togglePregnancyFields() {
    const isPregnant = document.getElementById('is_pregnant').checked;
    const pregnancyFields = document.getElementById('pregnancy-fields');
    const lmpInput = document.querySelector('input[name="lmp"]');

    pregnancyFields.classList.toggle('d-none', !isPregnant);
    lmpInput.required = isPregnant;
}

function calculateEDD() {
    const lmpInput = document.querySelector('input[name="lmp"]');
    const eddInput = document.querySelector('input[name="edd"]');

    if (!lmpInput.value) {
        eddInput.value = '';
        return;
    }

    const lmpDate = new Date(lmpInput.value);
    lmpDate.setDate(lmpDate.getDate() + 280);
    eddInput.value = lmpDate.toISOString().split('T')[0];
}

document.addEventListener('DOMContentLoaded', function () {
    togglePatientType();
    togglePregnancyFields();

    const lmpInput = document.querySelector('input[name="lmp"]');
    if (lmpInput) {
        lmpInput.addEventListener('change', calculateEDD);
    }
});
</script>
@endsection
