@extends('midwife.layout')

@section('title', 'View BHW Report - ReproCare')

@section('midwife-content')

{{-- ═══════════════════════════════
     PAGE HERO
════════════════════════════════ --}}
<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-file-earmark-text-fill me-2"></i>{{ $report->title }}
            </div>
            <p class="page-hero-subtitle">
                <span class="badge bg-info text-dark">{{ $report->reportPeriod }}</span>
                <span class="badge bg-{{ $report->status === 'completed' ? 'success' : 'warning' }}">
                    {{ ucfirst($report->status) }}
                </span>
                @if($report->printed_at)
                    <span class="badge bg-secondary">
                        <i class="bi bi-printer"></i> Printed {{ $report->printed_at->format('M d, Y') }}
                    </span>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('midwife.bhw-reports.index') }}" class="btn-hero-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Reports
            </a>
            <a href="{{ route('midwife.bhw-reports.print', $report->id) }}" class="btn-hero-primary" target="_blank">
                <i class="bi bi-printer-fill me-1"></i> Print Report
            </a>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════
     BHW INFO CARD
════════════════════════════════ --}}
<div class="card fade-in-card mb-4" style="border:none; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08);">
    <div class="card-body p-4">
        <h6 class="mb-3" style="font-weight:800;"><i class="bi bi-person-badge me-2"></i>BHW Information</h6>
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Name:</span>
                    <span class="fw-bold">{{ $report->bhw->name ?? 'Unknown' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Email:</span>
                    <span>{{ $report->bhw->email ?? 'N/A' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Barangay:</span>
                    <span>{{ $report->bhw->barangay ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Report Period:</span>
                    <span class="fw-bold">{{ $report->reportPeriod }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Records:</span>
                    <span class="fw-bold">{{ $report->total_records }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Unique Patients:</span>
                    <span>{{ $uniquePatients }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@if($report->description)
    <div class="alert alert-info fade-in-card mb-4" style="border-radius:10px;">
        <i class="bi bi-info-circle-fill me-2"></i><strong>Description:</strong> {{ $report->description }}
    </div>
@endif

{{-- ═══════════════════════════════
     STATISTICS CARDS
════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card stat-blue fade-in-card">
            <i class="bi bi-file-medical-fill stat-icon"></i>
            <div class="stat-label">Total Records</div>
            <div class="stat-number" data-count="{{ $report->total_records }}">{{ $report->total_records }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-cyan fade-in-card">
            <i class="bi bi-people-fill stat-icon"></i>
            <div class="stat-label">Unique Patients</div>
            <div class="stat-number" data-count="{{ $uniquePatients }}">{{ $uniquePatients }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-{{ $riskDistribution['high'] > 0 ? 'red' : 'green' }} fade-in-card">
            <i class="bi bi-exclamation-triangle-fill stat-icon"></i>
            <div class="stat-label">High Risk Cases</div>
            <div class="stat-number" data-count="{{ $riskDistribution['high'] }}">{{ $riskDistribution['high'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-amber fade-in-card">
            <i class="bi bi-flag-fill stat-icon"></i>
            <div class="stat-label">Medium Risk</div>
            <div class="stat-number" data-count="{{ $riskDistribution['medium'] }}">{{ $riskDistribution['medium'] }}</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════
     RISK DISTRIBUTION CHART
════════════════════════════════ --}}
<div class="card fade-in-card mb-4" style="border:none; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08);">
    <div class="card-body p-4">
        <h6 class="mb-3" style="font-weight:800;"><i class="bi bi-graph-up me-2"></i>Risk Level Distribution</h6>
        <div class="row">
            <div class="col-md-4">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-success rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                    <span class="me-auto">Low Risk</span>
                    <span class="badge bg-success">{{ $riskDistribution['low'] }}</span>
                </div>
                <div class="progress mb-3" style="height: 6px;">
                    @php
                        $total = array_sum($riskDistribution);
                        $lowPct = $total > 0 ? ($riskDistribution['low'] / $total) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-success" style="width: {{ $lowPct }}%"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-warning rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                    <span class="me-auto">Medium Risk</span>
                    <span class="badge bg-warning text-dark">{{ $riskDistribution['medium'] }}</span>
                </div>
                <div class="progress mb-3" style="height: 6px;">
                    @php
                        $medPct = $total > 0 ? ($riskDistribution['medium'] / $total) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-warning" style="width: {{ $medPct }}%"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-danger rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                    <span class="me-auto">High Risk</span>
                    <span class="badge bg-danger">{{ $riskDistribution['high'] }}</span>
                </div>
                <div class="progress mb-3" style="height: 6px;">
                    @php
                        $highPct = $total > 0 ? ($riskDistribution['high'] / $total) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-danger" style="width: {{ $highPct }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════
     HEALTH RECORDS TABLE
════════════════════════════════ --}}
<div class="card fade-in-card mb-4" style="border:none; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 style="font-weight:800;"><i class="bi bi-heart-pulse-fill me-2"></i>Health Records</h6>
            <span class="text-muted">{{ $healthRecords->total() }} records found</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover" style="margin:0;">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Blood Pressure</th>
                        <th>Weight</th>
                        <th>Heart Rate</th>
                        <th>Temp</th>
                        <th>Risk Level</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($healthRecords as $record)
                        <tr>
                            <td>
                                <div style="font-weight:800;">{{ $record->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $record->created_at->format('g:i A') }}</small>
                            </td>
                            <td>
                                <div style="font-weight:800;">{{ $record->user->name ?? 'Unknown' }}</div>
                                <small class="text-muted">{{ $record->user->email ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $record->bp }}
                                </span>
                            </td>
                            <td>{{ $record->weight }} kg</td>
                            <td>{{ $record->heart_rate }} bpm</td>
                            <td>{{ $record->temperature }}°C</td>
                            <td>
                                @if($record->risk_level === 'High')
                                    <span class="badge bg-danger">High</span>
                                @elseif($record->risk_level === 'Medium')
                                    <span class="badge bg-warning text-dark">Medium</span>
                                @else
                                    <span class="badge bg-success">Low</span>
                                @endif
                            </td>
                        </tr>
                        @if($record->notes)
                            <tr style="background:var(--bg-card2);">
                                <td colspan="7" class="py-2">
                                    <small class="text-muted">
                                        <strong>Notes:</strong> {{ $record->notes }}
                                    </small>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size:3rem; color:rgba(0,0,0,0.1);"></i>
                                <p class="text-muted mt-2">No health records found for this report period</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($healthRecords->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $healthRecords->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════
     FOOTER ACTIONS
════════════════════════════════ --}}
<div class="d-flex justify-content-between">
    <form action="{{ route('midwife.bhw-reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this report?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger">
            <i class="bi bi-trash me-1"></i> Delete Report
        </button>
    </form>
    <a href="{{ route('midwife.bhw-reports.print', $report->id) }}" class="btn btn-primary" target="_blank">
        <i class="bi bi-printer-fill me-1"></i> Print Report
    </a>
</div>

@endsection
