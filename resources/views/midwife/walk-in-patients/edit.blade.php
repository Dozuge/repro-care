@extends('midwife.layout')

@section('title', 'Edit Walk-in Woman - Midwife Portal | ReproCare')

@section('midwife-content')
<div class="py-4">
    <div class="page-hero fade-in-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
            <div>
                <h1 class="page-hero-title">
                    <i class="bi bi-pencil-square me-2"></i>Edit Walk-in Woman
                </h1>
                <p class="page-hero-subtitle">Update the walk-in woman's basic information and purok assignment.</p>
            </div>
            <a href="{{ route('midwife.walk-in-patients.show', $patient->id) }}" class="btn-hero-primary">
                <i class="bi bi-arrow-left"></i> Back to Women
            </a>
        </div>
    </div>

    <div class="card fade-in-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-person-vcard-fill me-2" style="color:var(--primary-light);"></i>Walk-in Information</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('midwife.walk-in-patients.update', $patient->id) }}">
                @method('PUT')
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" required value="{{ old('first_name', $patient->first_name) }}">
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Middle Initial</label>
                        <input type="text" name="middle_initial" class="form-control @error('middle_initial') is-invalid @enderror" maxlength="10" value="{{ old('middle_initial', $patient->middle_initial ?? '') }}">
                        @error('middle_initial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Last Name *</label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" required value="{{ old('last_name', $patient->last_name) }}">
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}">
                        @error('date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" maxlength="20" value="{{ old('contact_number', $patient->contact_number ?? '') }}">
                        @error('contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Purok</label>
                        <select name="purok_id" class="form-select @error('purok_id') is-invalid @enderror">
                            <option value="">Select purok</option>
                            @foreach($puroks as $purok)
                                <option value="{{ $purok->id }}" {{ (string) old('purok_id', $patient->purok_id) === (string) $purok->id ? 'selected' : '' }}>{{ $purok->name }}</option>
                            @endforeach
                        </select>
                        @error('purok_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Barangay updates automatically from the selected purok.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Reason for Visit</label>
                        <textarea name="reason_for_visit" class="form-control @error('reason_for_visit') is-invalid @enderror" rows="3" placeholder="e.g., Prenatal checkup, follow-up, consultation...">{{ old('reason_for_visit', $patient->reason_for_visit ?? '') }}</textarea>
                        @error('reason_for_visit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between flex-wrap gap-2 mt-4">
                    <a href="{{ route('midwife.walk-in-patients.show', $patient->id) }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update Walk-in
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
