@extends('midwife.layout')

@section('title', 'Schedule Checkup - ReproCare')

@section('midwife-content')

{{-- ═══════════════════════════════
     PAGE HERO
═══════════════════════════════ --}}
<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-calendar-check-fill me-2"></i>
                @if(isset($woman) && $woman)
                    Schedule Checkup for {{ $woman->name }}
                @else
                    Schedule New Checkup
                @endif
            </div>
            <p class="page-hero-subtitle">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, F j, Y') }}
            </p>
        </div>
        <div class="d-flex gap-2">
            @if(isset($woman) && $woman)
                <a href="{{ route('midwife.patient-details', $woman->id) }}" class="btn-hero-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            @else
                <a href="{{ route('midwife.checkups.index') }}" class="btn-hero-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Checkups
                </a>
            @endif
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
     CHECKUP FORM CARD
═══════════════════════════════ --}}
<div class="card fade-in-card mb-4" style="border:none; background:var(--bg-card);">
    <div class="card-header" style="background:transparent; border-bottom:1px solid var(--border-color); padding:1rem 1.5rem;">
        <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text);">
            <i class="bi bi-calendar-plus-fill me-2" style="color:var(--info);"></i>
            Checkup Details
        </h5>
    </div>
    <div class="card-body p-4">
            <form action="{{ route('midwife.checkups.store') }}" method="POST">
                @csrf
                
                {{-- ═══════════════════════════════
                     PATIENT INFORMATION
                ════════════════════════════════ --}}
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <div style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.75rem;">
                            <i class="bi bi-person-fill me-1" style="color:var(--primary);"></i> Patient Information
                        </div>
                    </div>
                    
                    @if(isset($woman) && $woman)
                        <input type="hidden" name="patient_type" value="registered">
                        <input type="hidden" name="user_id" value="{{ $woman->id }}">
                        <div class="col-12">
                            <div class="p-3 mb-3" style="background:linear-gradient(135deg, rgba(6,182,212,0.1), rgba(102,16,242,0.05)); border:1px solid rgba(6,182,212,0.3); border-radius:12px;">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,var(--info),var(--primary));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-person" style="color:#fff;font-size:1.2rem;"></i>
                                    </div>
                                    <div>
                                        <small style="color:var(--text-muted); text-transform:uppercase; font-size:0.7rem; letter-spacing:0.5px;">Patient</small>
                                        <div style="font-weight:600; color:var(--text);">{{ $woman->name }}</div>
                                        <small style="color:var(--text-muted);">{{ $woman->email }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-md-12 mb-3">
                            <label class="form-label" style="font-size:0.8rem; font-weight:500; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">
                                Patient Type <span style="color:var(--danger);">*</span>
                            </label>
                            <div class="d-flex gap-2 flex-wrap">
                                <input type="radio" class="btn-check" name="patient_type" id="patient_type_registered" value="registered" {{ old('patient_type', 'registered') === 'registered' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="patient_type_registered">Registered</label>
                                <input type="radio" class="btn-check" name="patient_type" id="patient_type_unregistered" value="walk_in" {{ old('patient_type') === 'walk_in' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="patient_type_unregistered">Walk-in</label>
                            </div>
                        </div>
                        {{-- Patient Search --}}
                        <div class="col-md-12 mb-3" id="registeredSearchWrap">
                            <label for="patientSearch" class="form-label" style="font-size:0.8rem; font-weight:500; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="bi bi-search me-1" style="color:var(--primary);"></i>Search Patient
                            </label>
                            <input type="text" class="form-control" id="patientSearch"
                                   placeholder="Type to search patient by name or email..."
                                   style="background:var(--bg-input); border:1px solid var(--input-border); color:var(--text); border-radius:10px;">
                        </div>

                        <div class="col-md-12 mb-3" id="registeredSelectWrap">
                            <label for="user_id" class="form-label" style="font-size:0.8rem; font-weight:500; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">
                                Select Patient <span style="color:var(--danger);">*</span>
                            </label>
                            <select class="form-select @error('user_id') is-invalid @enderror"
                                    id="user_id" name="user_id" required
                                    style="background:var(--bg-input); border:1px solid var(--input-border); color:var(--text); border-radius:10px;">
                                <option value="" style="background:var(--bg-card); color:var(--text);">Choose a patient...</option>
                                @if(isset($women) && $women)
                                    @foreach($women as $woman)
                                        <option value="{{ $woman->id }}"
                                                data-search="{{ strtolower($woman->name . ' ' . $woman->email) }}"
                                                {{ old('user_id') == $woman->id ? 'selected' : '' }}
                                                style="background:var(--bg-card); color:var(--text);">
                                            {{ $woman->name }} - {{ $woman->email }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3 d-none" id="walkInSearchWrap">
                            <label for="walkInSearch" class="form-label" style="font-size:0.8rem; font-weight:500; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="bi bi-search me-1" style="color:var(--primary);"></i>Search Walk-in Woman
                            </label>
                            <input type="text" class="form-control" id="walkInSearch"
                                   placeholder="Type to search walk-in woman..."
                                   style="background:var(--bg-input); border:1px solid var(--input-border); color:var(--text); border-radius:10px;">
                        </div>
                        <div class="col-md-12 mb-3 d-none" id="unregisteredSelectWrap">
                            <label for="walk_in_patient_id" class="form-label" style="font-size:0.8rem; font-weight:500; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">
                                Select Walk-in Woman <span style="color:var(--danger);">*</span>
                            </label>
                            <select class="form-select @error('walk_in_patient_id') is-invalid @enderror"
                                    id="walk_in_patient_id" name="walk_in_patient_id"
                                    style="background:var(--bg-input); border:1px solid var(--input-border); color:var(--text); border-radius:10px;">
                                <option value="">Choose a walk-in woman...</option>
                                @foreach(($walkInPatients ?? collect()) as $patient)
                                    <option value="{{ $patient->id }}" data-search="{{ strtolower(($patient->full_name ?? '') . ' ' . ($patient->barangay ?? '') . ' ' . ($patient->contact_number ?? '')) }}" {{ old('walk_in_patient_id') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->full_name }}{{ $patient->barangay ? ' - ' . $patient->barangay : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('walk_in_patient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                </div>

                <hr style="border-color:var(--border-color); margin:1.5rem 0;">

                {{-- ═══════════════════════════════
                     CHECKUP DETAILS
                ════════════════════════════════ --}}
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <div style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.75rem;">
                            <i class="bi bi-calendar-event-fill me-1" style="color:var(--success);"></i> Checkup Details
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="scheduled_date" class="form-label" style="font-size:0.8rem; font-weight:500; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">
                            Scheduled Date <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="date" class="form-control @error('scheduled_date') is-invalid @enderror" 
                               id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date') }}" 
                               min="{{ now()->format('Y-m-d') }}" required
                               style="background:var(--bg-input); border:1px solid var(--input-border); color:var(--text); border-radius:10px;">
                        @error('scheduled_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small style="color:var(--text-muted); font-size:0.8rem;"><i class="bi bi-info-circle me-1"></i>Cannot be in the past</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="scheduled_time" class="form-label" style="font-size:0.8rem; font-weight:500; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">
                            Scheduled Time <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="time" class="form-control @error('scheduled_time') is-invalid @enderror" 
                               id="scheduled_time" name="scheduled_time" value="{{ old('scheduled_time') }}" required
                               style="background:var(--bg-input); border:1px solid var(--input-border); color:var(--text); border-radius:10px;">
                        @error('scheduled_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small style="color:var(--text-muted); font-size:0.8rem;"><i class="bi bi-clock me-1"></i>Working hours: 8:00 AM - 5:00 PM</small>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label for="purpose" class="form-label" style="font-size:0.8rem; font-weight:500; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">
                            Purpose of Checkup <span style="color:var(--danger);">*</span>
                        </label>
                        <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                  id="purpose" name="purpose" rows="3" required
                                  placeholder="Describe the reason for this checkup..."
                                  style="background:var(--bg-input); border:1px solid var(--input-border); color:var(--text); border-radius:10px;">{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr style="border-color:var(--border-color); margin:1.5rem 0;">

                {{-- ═══════════════════════════════
                     ADDITIONAL NOTES
                ════════════════════════════════ --}}
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <div style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.75rem;">
                            <i class="bi bi-clipboard-fill me-1" style="color:var(--warning);"></i> Additional Notes
                        </div>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label for="notes" class="form-label" style="font-size:0.8rem; font-weight:500; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">
                            Special Instructions
                        </label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" placeholder="Any special instructions or preparations needed..."
                                  style="background:var(--bg-input); border:1px solid var(--input-border); color:var(--text); border-radius:10px;">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- ═══════════════════════════════
                     FORM ACTIONS
                ════════════════════════════════ --}}
                <div class="d-flex justify-content-between align-items-center pt-3" style="border-top:1px solid var(--border-color);">
                    <a href="{{ route('midwife.checkups.index') }}" 
                       class="btn" 
                       style="background:transparent; border:1px solid var(--border-color); color:var(--text-muted); border-radius:10px; padding:0.6rem 1.2rem;">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                    <div class="d-flex gap-2">
                        <button type="reset" 
                                class="btn" 
                                style="background:transparent; border:1px solid var(--warning); color:var(--warning); border-radius:10px; padding:0.6rem 1.2rem;">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </button>
                        <button type="submit" 
                                class="btn" 
                                style="background:linear-gradient(135deg,var(--success),var(--primary)); color:#fff; border:none; border-radius:10px; padding:0.6rem 1.5rem; font-weight:500;">
                            <i class="bi bi-check-circle me-1"></i> Schedule Checkup
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const searchInput = document.getElementById('patientSearch');
    const patientSelect = document.getElementById('user_id');
    const patientTypeRegistered = document.getElementById('patient_type_registered');
    const patientTypeUnregistered = document.getElementById('patient_type_unregistered');
    const registeredSearchWrap = document.getElementById('registeredSearchWrap');
    const registeredSelectWrap = document.getElementById('registeredSelectWrap');
    const unregisteredSelectWrap = document.getElementById('unregisteredSelectWrap');
    const walkInSelect = document.getElementById('walk_in_patient_id');
    const walkInSearchWrap = document.getElementById('walkInSearchWrap');
    const walkInSearch = document.getElementById('walkInSearch');

    function syncPatientType() {
        if (!patientTypeRegistered || !patientTypeUnregistered || !registeredSelectWrap || !unregisteredSelectWrap) {
            return;
        }

        const isRegistered = patientTypeRegistered.checked;
        registeredSearchWrap?.classList.toggle('d-none', !isRegistered);
        registeredSelectWrap.classList.toggle('d-none', !isRegistered);
        unregisteredSelectWrap.classList.toggle('d-none', isRegistered);
        walkInSearchWrap?.classList.toggle('d-none', isRegistered);

        if (patientSelect) {
            patientSelect.disabled = !isRegistered;
            patientSelect.required = isRegistered;
        }
        if (walkInSelect) {
            walkInSelect.disabled = isRegistered;
            walkInSelect.required = !isRegistered;
        }
    }

    if (searchInput && patientSelect) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = patientSelect.querySelectorAll('option');

            options.forEach(function(option) {
                if (option.value === '') {
                    option.style.display = 'block';
                    return;
                }

                const searchData = option.getAttribute('data-search') || '';
                if (searchData.includes(searchTerm)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        });
    }

    if (walkInSearch && walkInSelect) {
        walkInSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = walkInSelect.querySelectorAll('option');

            options.forEach(function(option) {
                if (option.value === '') {
                    option.hidden = false;
                    return;
                }

                const searchData = option.getAttribute('data-search') || '';
                option.hidden = !searchData.includes(searchTerm);
            });
        });
    }

    patientTypeRegistered?.addEventListener('change', syncPatientType);
    patientTypeUnregistered?.addEventListener('change', syncPatientType);
    syncPatientType();
</script>
@endpush
@endsection
