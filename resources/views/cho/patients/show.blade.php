@extends('cho.layout')

@section('title', 'Patient Case Review - CHO | ReproCare')

@section('cho-content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('cho.patients.index') }}" class="btn btn-sm btn-outline-secondary mb-2" style="border-radius:10px;">
            <i class="bi bi-arrow-left me-1"></i> Back to Patient Registry
        </a>
        <h2 class="fw-800 mb-0 text-dark" style="font-family:'Plus Jakarta Sans',sans-serif;">
            {{ $patient->first_name }} {{ $patient->last_name }}
            @if($patient->isTeenage())
                <span class="badge" style="background:var(--badge-critical-bg); color:var(--badge-critical-text); font-size:0.75rem; font-weight:700;">
                    Adolescent Pregnancy (&lt;19)
                </span>
            @endif
        </h2>
    </div>
    <div class="d-flex gap-2">
        {{-- Consultation Stream Direct Link --}}
        <a href="{{ route('learning.index', ['type' => 'video']) }}" class="btn btn-sm btn-light border text-danger fw-600 d-inline-flex align-items-center gap-1" style="border-radius:10px;">
            <i class="bi bi-play-circle-fill"></i> Video Counseling Library
        </a>

        {{-- Archive Action --}}
        @if(!$patient->trashed())
            <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#archiveModal" style="border-radius:10px;">
                <i class="bi bi-archive-fill"></i> Archive Case
            </button>

            <div class="modal fade" id="archiveModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content border-0 shadow" style="border-radius:18px;">
                        <div class="modal-body text-center p-4">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 text-warning" style="width:54px; height:54px; background:rgba(245,158,11,0.12);">
                                <i class="bi bi-archive fs-3"></i>
                            </div>
                            <h5 class="fw-800 text-dark mb-1">Archive Patient Record?</h5>
                            <p class="text-muted text-xs mb-4">
                                This will safely move {{ $patient->name }}'s records into the data archive.
                            </p>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-light w-50" data-bs-dismiss="modal" style="border-radius:10px;">Cancel</button>
                                <form action="{{ route('cho.patients.archive', $patient->id) }}" method="POST" class="w-50">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100 text-white fw-700" style="border-radius:10px;">
                                        Archive
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <span class="badge bg-secondary">Archived Case</span>
        @endif
    </div>
</div>

<div class="row g-4">
    {{-- Left: Patient Summary & AI Clinical Decision Support --}}
    <div class="col-lg-4">
        {{-- Profile Card --}}
        <div class="card shadow-sm border mb-4" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
            <div class="card-header bg-transparent fw-700 border-bottom py-3">
                <i class="bi bi-person-circle me-2 text-primary"></i>Patient Demographic Profile
            </div>
            <div class="card-body p-3">
                <ul class="list-group list-group-flush" style="font-size:0.875rem;">
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Patient ID</span>
                        <span class="fw-700 text-dark">#{{ $patient->id }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Age / DOB</span>
                        <span class="fw-600">{{ $patient->age ? $patient->age . ' yrs' : 'N/A' }} ({{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'N/A' }})</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Contact Number</span>
                        <span class="fw-600">{{ $patient->contact_number ?? 'None' }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Barangay</span>
                        <span class="fw-600">{{ $patient->barangay ?? 'N/A' }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">PhilHealth ID</span>
                        <span class="fw-600">{{ $patient->philhealth_number ?? 'Not Enrolled' }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Registered Since</span>
                        <span class="fw-600">{{ $patient->created_at->format('M d, Y') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- AI Decision Support & Clinical Triage Card --}}
        @php
            $riskService = app(\App\Services\RiskAnalysisService::class);
            $riskTier = $riskService->evaluate($patient->id);
            $badgeColor = match(strtolower($riskTier)) {
                'critical', 'high' => 'danger',
                'medium'           => 'warning',
                default            => 'success',
            };
            $latestHealthRec = $healthRecords->first();
        @endphp
        <div class="card shadow-sm border border-{{ $badgeColor }} mb-4" style="border-radius:16px; background:var(--bg-card);">
            <div class="card-header bg-{{ $badgeColor }} text-white fw-700 d-flex justify-content-between align-items-center py-3">
                <span><i class="bi bi-robot me-2"></i>AI Clinical Decision Support</span>
                <span class="badge bg-white text-dark text-uppercase px-2.5 py-1">{{ $riskTier }} Risk</span>
            </div>
            <div class="card-body p-3.5">
                <h6 class="fw-700 text-xs text-muted text-uppercase mb-2">Automated Action Directives</h6>
                @if($latestHealthRec && !empty($latestHealthRec->recommendations))
                    <div class="p-3 rounded-3" style="font-size:0.85rem; line-height:1.6; background:var(--bg-card2); border:1px solid var(--border); white-space:pre-line;">
                        {{ $latestHealthRec->recommendations }}
                    </div>
                @else
                    <div class="p-3 rounded-3 bg-light text-muted" style="font-size:0.85rem;">
                        Routine low-risk care. Standard antenatal schedule recommended.
                    </div>
                @endif
            </div>
        </div>

        {{-- Counseling Video Stream Helper --}}
        @if(isset($availableVideos) && $availableVideos->count() > 0)
            <div class="card shadow-sm border" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
                <div class="card-header bg-transparent fw-700 border-bottom py-3">
                    <i class="bi bi-camera-video-fill me-2 text-danger"></i>Patient Counseling Videos
                </div>
                <div class="card-body p-3">
                    <ul class="list-group list-group-flush" style="font-size:0.84rem;">
                        @foreach($availableVideos->take(3) as $vid)
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <span class="text-truncate" style="max-width:180px;">{{ $vid->title }}</span>
                                <a href="{{ route('learning.show', $vid->id) }}" class="btn btn-xs btn-outline-danger" style="border-radius:6px; font-size:0.75rem;">
                                    <i class="bi bi-play-fill"></i> Stream
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>

    {{-- Right: Pregnancy History & Health Records --}}
    <div class="col-lg-8">
        {{-- Active Pregnancies --}}
        <div class="card shadow-sm border mb-4" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
            <div class="card-header bg-transparent fw-700 border-bottom py-3">
                <i class="bi bi-heart-pulse-fill me-2 text-danger"></i>Pregnancy History &amp; Gestational Stages
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-2.5">Pregnancy #</th>
                                <th class="py-2.5">Expected Delivery (EDD)</th>
                                <th class="py-2.5">Gestational Age</th>
                                <th class="py-2.5">Risk Level</th>
                                <th class="py-2.5">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pregnancies as $preg)
                                <tr>
                                    <td class="px-3 fw-700">#{{ $preg->id }}</td>
                                    <td>{{ $preg->expected_delivery_date ? \Carbon\Carbon::parse($preg->expected_delivery_date)->format('M d, Y') : 'N/A' }}</td>
                                    <td><span class="text-primary fw-600">🤰 {{ $preg->aog_weeks ?? 0 }} weeks AOG</span></td>
                                    <td>
                                        <span class="badge bg-{{ in_array(strtolower($preg->risk_level), ['high','critical']) ? 'danger' : 'success' }}">
                                            {{ $preg->risk_level ?? 'Low' }}
                                        </span>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($preg->status ?? 'active') }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No pregnancy records logged.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Clinical Health Records --}}
        <div class="card shadow-sm border" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
            <div class="card-header bg-transparent fw-700 border-bottom py-3">
                <i class="bi bi-file-earmark-medical me-2 text-primary"></i>Medical Examination Logs &amp; Vitals History
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-2.5">Date</th>
                                <th class="py-2.5">Blood Pressure</th>
                                <th class="py-2.5">Weight</th>
                                <th class="py-2.5">Hemoglobin</th>
                                <th class="py-2.5">Risk Classification</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($healthRecords as $rec)
                                <tr>
                                    <td class="px-3">{{ $rec->created_at->format('M d, Y') }}</td>
                                    <td class="fw-700 text-dark">{{ $rec->bp ?? 'N/A' }}</td>
                                    <td>{{ $rec->weight ? $rec->weight . ' kg' : 'N/A' }}</td>
                                    <td>{{ $rec->hemoglobin ? $rec->hemoglobin . ' g/dL' : 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ in_array(strtolower($rec->risk_level), ['high','critical']) ? 'danger' : (strtolower($rec->risk_level) === 'medium' ? 'warning' : 'success') }}">
                                            {{ $rec->risk_level ?? 'Low' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No clinical logs available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
