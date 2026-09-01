@extends('midwife.layout')

@section('title', 'BHW Monthly Reports - ReproCare')

@section('midwife-content')
<div class="workspace-stack">
    <div class="page-hero fade-in-card">
        <div class="workspace-toolbar" style="position:relative;z-index:1;">
            <div>
                <div class="page-hero-title"><i class="bi bi-file-earmark-text-fill me-2"></i>BHW Monthly Reports</div>
                <p class="page-hero-subtitle">Final review queue for reports approved by the BHW president and waiting for midwife action.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="metric-grid">
        <div class="metric-card metric-card-primary fade-in-card">
            <i class="bi bi-folder2-open metric-card-icon"></i>
            <div class="metric-card-label">Reports in Queue</div>
            <div class="metric-card-value">{{ $reports->total() }}</div>
            <div class="metric-card-note">Reports loaded in the current review window.</div>
        </div>
        <div class="metric-card metric-card-cyan fade-in-card">
            <i class="bi bi-hourglass-split metric-card-icon"></i>
            <div class="metric-card-label">Pending Review</div>
            <div class="metric-card-value">{{ $reports->getCollection()->where('submission_status', 'submitted_to_midwife')->count() }}</div>
            <div class="metric-card-note">Reports waiting for approval or rejection.</div>
        </div>
        <div class="metric-card metric-card-green fade-in-card">
            <i class="bi bi-check2-square metric-card-icon"></i>
            <div class="metric-card-label">Approved</div>
            <div class="metric-card-value">{{ $reports->getCollection()->where('submission_status', 'approved_by_midwife')->count() }}</div>
            <div class="metric-card-note">Reports already cleared by the midwife.</div>
        </div>
        <div class="metric-card metric-card-rose fade-in-card">
            <i class="bi bi-arrow-return-left metric-card-icon"></i>
            <div class="metric-card-label">Rejected</div>
            <div class="metric-card-value">{{ $reports->getCollection()->where('submission_status', 'rejected')->count() }}</div>
            <div class="metric-card-note">Reports returned for revisions or corrections.</div>
        </div>
    </div>

    <div class="workspace-panel fade-in-card">
        <div class="workspace-panel-header">
            <h2 class="workspace-panel-title"><i class="bi bi-funnel-fill"></i>Filter Queue</h2>
            <p class="workspace-panel-subtitle">Focus on health record reports or pregnancy reports only.</p>
        </div>
        <div class="workspace-panel-body">
            <form method="GET" action="{{ route('midwife.bhw-reports.index') }}" class="workspace-filter-grid">
                <div class="span-3">
                    <label class="form-label">Report Type</label>
                    <select name="filter" class="form-select">
                        <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Reports</option>
                        <option value="health_records" {{ $filter === 'health_records' ? 'selected' : '' }}>Health Records</option>
                        <option value="pregnancies" {{ $filter === 'pregnancies' ? 'selected' : '' }}>Pregnancies</option>
                    </select>
                </div>
                <div class="span-3">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        <option value="">All Months</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ (string) $month === (string) $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="span-2">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        <option value="">All Years</option>
                        @foreach(range(2020, now()->year + 1) as $y)
                            <option value="{{ $y }}" {{ (string) $year === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="span-2">
                    <label class="form-label">Status</label>
                    <select name="submission_status" class="form-select">
                        <option value="all" {{ $submissionStatus === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="submitted_to_midwife" {{ $submissionStatus === 'submitted_to_midwife' ? 'selected' : '' }}>Pending</option>
                        <option value="approved_by_midwife" {{ $submissionStatus === 'approved_by_midwife' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $submissionStatus === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="span-2 workspace-filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Apply Filter</button>
                    <a href="{{ route('midwife.bhw-reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i>Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="workspace-panel fade-in-card">
        <div class="workspace-panel-header">
            <h2 class="workspace-panel-title"><i class="bi bi-table"></i>Review List</h2>
            <p class="workspace-panel-subtitle">Open a report to inspect its content before you approve or reject it.</p>
        </div>
        <div class="workspace-panel-body pt-3">
            @if($reports->count() > 0)
                <div class="modern-table-wrap">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Report Title</th>
                                <th>BHW</th>
                                <th>Period</th>
                                <th>Records</th>
                                <th>Submission Status</th>
                                <th>President Notes</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td>
                                        <div class="table-title">{{ $report->title }}</div>
                                        @if($report->description)
                                            <div class="table-subtitle">{{ \Illuminate\Support\Str::limit($report->description, 60) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="table-title">{{ $report->bhw->name ?? 'Unknown' }}</div>
                                        <div class="table-subtitle">{{ ucfirst(str_replace('_', ' ', $report->report_type ?? 'report')) }}</div>
                                    </td>
                                    <td><span class="summary-chip chip-info">{{ $report->reportPeriod }}</span></td>
                                    <td>{{ $report->total_records }} records</td>
                                    <td>
                                        @php
                                            $statusChip = match($report->submission_status) {
                                                'approved_by_midwife' => 'chip-success',
                                                'rejected' => 'chip-danger',
                                                default => 'chip-warning',
                                            };
                                        @endphp
                                        <span class="summary-chip {{ $statusChip }}">{{ ucwords(str_replace('_', ' ', $report->submission_status)) }}</span>
                                    </td>
                                    <td>{{ $report->president_notes ? \Illuminate\Support\Str::limit($report->president_notes, 50) : 'No notes' }}</td>
                                    <td class="text-end">
                                        <div class="table-actions">
                                            <a href="{{ route('midwife.bhw-reports.show', $report->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                            @if($report->submission_status === 'submitted_to_midwife')
                                                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $report->id }}"><i class="bi bi-check-lg"></i></button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $report->id }}"><i class="bi bi-x-lg"></i></button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center pt-4">
                    {{ $reports->links() }}
                </div>
            @else
                <div class="empty-state-panel">
                    <i class="bi bi-file-earmark-x"></i>
                    <h3>No reports found</h3>
                    <p>Reports approved by the BHW president will appear here when they reach the midwife queue.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@foreach($reports as $report)
    @if($report->submission_status === 'submitted_to_midwife')
        <div class="modal fade report-action-modal" id="approveModal{{ $report->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('midwife.bhw-reports.approve', $report->id) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Approve Report</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Approve this report and finalize its workflow?</p>
                            <label class="form-label">Notes (optional)</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Approve Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade report-action-modal" id="rejectModal{{ $report->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('midwife.bhw-reports.reject', $report->id) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Reject Report</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Reject this report and return it to the BHW president with comments.</p>
                            <label class="form-label">Reason for rejection</label>
                            <textarea name="notes" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.report-action-modal').forEach(function(modal) {
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });
});
</script>
@endpush
@endsection
