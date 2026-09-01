@extends('bhw-president.layout')

@section('title', 'High-Risk Pregnancies - BHW President Portal | ReproCare')

@section('bhw-president-content')

<div class="page-hero fade-in-card" style="background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">High-Risk Pregnancies</div>
            <p class="page-hero-subtitle">Monitor pregnancies requiring special attention</p>
        </div>
        <a href="{{ route('bhw-president.dashboard') }}" class="btn btn-sm btn-light">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<div class="card fade-in-card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2" style="color:var(--danger);"></i>High-Risk Cases ({{ $highRiskPregnancies->total() }})</h5>
    </div>
    <div class="card-body">
        @if($highRiskPregnancies->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>EDD</th>
                        <th>Gestational Age</th>
                        <th>Risk Factors</th>
                        <th>Last Checkup</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($highRiskPregnancies as $pregnancy)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#ef4444);display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#fff;">
                                    {{ strtoupper(substr(optional($pregnancy->woman)->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:500;">{{ optional($pregnancy->woman)->name ?? 'Unknown' }}</div>
                                    <small class="text-muted">{{ optional($pregnancy->woman)->barangay ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ optional($pregnancy->edd)?->format('M j, Y') ?? 'N/A' }}</td>
                        <td>{{ $pregnancy->gestational_age ?? 'N/A' }} weeks</td>
                        <td>
                            <span class="badge bg-danger">High Risk</span>
                            @if($pregnancy->is_high_risk)
                            <span class="badge bg-warning ms-1">Flagged</span>
                            @endif
                        </td>
                        <td>{{ optional($pregnancy->checkups->first())->scheduled_date?->format('M j, Y') ?? 'No checkups' }}</td>
                        <td>
                            <a href="{{ route('midwife.patient-details', $pregnancy->user_id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $highRiskPregnancies->links() }}
        @else
        <div class="text-center py-5">
            <i class="bi bi-check-circle" style="font-size:3rem;color:var(--success);"></i>
            <h4 class="mt-3">No High-Risk Pregnancies</h4>
            <p class="text-muted">All active pregnancies are currently low-risk.</p>
        </div>
        @endif
    </div>
</div>

@endsection
