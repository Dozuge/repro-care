@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title">Convert Walk-in to Registered User</h2>
        <a href="{{ route('midwife.walk-in-patients.show', $walkInPatient->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Patient Details
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Complete Registration</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        This will convert the walk-in patient <strong>{{ $walkInPatient->full_name }}</strong> into a registered user account.
                    </div>

                    <form method="POST" action="{{ route('midwife.walk-in-patients.store-convert', $walkInPatient->id) }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" value="{{ $walkInPatient->first_name }}" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Middle Initial</label>
                                <input type="text" class="form-control" value="{{ $walkInPatient->middle_initial ?? '' }}" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" value="{{ $walkInPatient->last_name }}" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control" required placeholder="patient@example.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Number *</label>
                                <input type="text" name="contact_number" class="form-control" required value="{{ $walkInPatient->contact_number ?? '' }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password *</label>
                                <input type="password" name="password" class="form-control" required minlength="8" placeholder="Min 8 characters">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password *</label>
                                <input type="password" name="password_confirmation" class="form-control" required minlength="8" placeholder="Re-enter password">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Address *</label>
                                <input type="text" name="address" class="form-control" required value="{{ $walkInPatient->address ?? '' }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Barangay *</label>
                                <input type="text" name="barangay" class="form-control" required value="{{ $walkInPatient->barangay ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ $walkInPatient->date_of_birth?->format('Y-m-d') ?? '' }}" readonly>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('midwife.walk-in-patients.show', $walkInPatient->id) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-user-plus"></i> Convert to Registered User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Walk-in Patient Info</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $walkInPatient->full_name }}</p>
                    <p><strong>Age:</strong> {{ $walkInPatient->age ?? '—' }}</p>
                    <p><strong>Contact:</strong> {{ $walkInPatient->contact_number ?? '—' }}</p>
                    <p><strong>Address:</strong> {{ $walkInPatient->address ?? '—' }}</p>
                    <p><strong>Barangay:</strong> {{ $walkInPatient->barangay ?? '—' }}</p>
                    <p><strong>Purok:</strong> {{ $walkInPatient->purok?->name ?? '—' }}</p>
                    <p><strong>Reason for Visit:</strong> {{ $walkInPatient->reason_for_visit ?? '—' }}</p>
                    <hr>
                    <p><strong>Recorded By:</strong> {{ $walkInPatient->recordedBy?->name ?? '—' }}</p>
                    <p><strong>Recorded On:</strong> {{ $walkInPatient->created_at->format('M d, Y g:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
