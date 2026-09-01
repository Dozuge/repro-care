@extends('rhu.layout')

@section('title', 'Request Supplies - RHU Portal | ReproCare')

@section('rhu-content')

<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-box-seam-fill me-2" style="color:var(--primary-light);"></i>Request Supplies
            </div>
            <p class="page-hero-subtitle">
                Submit a new inventory replenishment request to the City Health Office.
            </p>
        </div>
        <a href="{{ route('rhu.supply-requests.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Supply Requests
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius:12px;">
        <h6 class="alert-heading fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please resolve the following errors:</h6>
        <ul class="mb-0 text-xs">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card fade-in-card">
    <div class="card-body">
        <form method="POST" action="{{ route('rhu.supply-requests.store') }}">
            @csrf

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label required-label">Item / Supply Name</label>
                    <input type="text" name="supply_name" class="form-control" value="{{ old('supply_name') }}" placeholder="e.g. Iron + Folic Acid tablets" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label required-label">Category</label>
                    <select name="supply_category" class="form-select" required>
                        <option value="" disabled selected>Select Category</option>
                        <option value="supplements" {{ old('supply_category') === 'supplements' ? 'selected' : '' }}>Supplements / Micronutrients</option>
                        <option value="contraceptives" {{ old('supply_category') === 'contraceptives' ? 'selected' : '' }}>Family Planning / Contraceptives</option>
                        <option value="medical_supplies" {{ old('supply_category') === 'medical_supplies' ? 'selected' : '' }}>Clinical & Medical Supplies</option>
                        <option value="vaccines" {{ old('supply_category') === 'vaccines' ? 'selected' : '' }}>Maternal Vaccines (Tetanus Toxoid, etc.)</option>
                        <option value="others" {{ old('supply_category') === 'others' ? 'selected' : '' }}>Others</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label required-label">Quantity Requested</label>
                    <input type="number" name="quantity_requested" class="form-control" value="{{ old('quantity_requested') }}" min="1" placeholder="e.g. 500" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label required-label">Unit of Measure</label>
                    <input type="text" name="unit" class="form-control" value="{{ old('unit') }}" placeholder="e.g. tablets, boxes, vials, kits" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label required-label">Urgency Level</label>
                    <select name="urgency" class="form-select" required>
                        <option value="routine" {{ old('urgency') === 'routine' ? 'selected' : '' }}>Routine (Stock replenishment)</option>
                        <option value="urgent" {{ old('urgency') === 'urgent' ? 'selected' : '' }}>Urgent (Low stock level)</option>
                        <option value="emergency" {{ old('urgency') === 'emergency' ? 'selected' : '' }}>Emergency (Stockout / Critical need)</option>
                    </select>
                </div>
            </div>

            <div class="mb-5">
                <label class="form-label required-label">Reason / Clinical Justification</label>
                <textarea name="reason" rows="4" class="form-control" placeholder="Provide context about current stock levels, patient intake numbers, or specific health center needs." required>{{ old('reason') }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Submit Request
                </button>
                <a href="{{ route('rhu.supply-requests.index') }}" class="btn btn-outline-secondary px-4">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>

@endsection
