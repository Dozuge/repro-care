@extends('midwife.layout')

@section('title', 'Reports - Midwife Portal')

@section('midwife-content')
<div class="workspace-stack">
    <div class="page-hero fade-in-card">
        <div class="workspace-toolbar" style="position:relative;z-index:1;">
            <div>
                <div class="page-hero-title"><i class="bi bi-clipboard2-pulse-fill me-2"></i>Reports Center</div>
                <p class="page-hero-subtitle">Review patient records, monitor maternal status, and export official summaries.</p>
            </div>
            <div class="workspace-toolbar-actions">
                <a href="{{ route('midwife.reports.export.csv') }}?search={{ request('search') }}&status={{ request('status') }}&start_date={{ request('start_date') }}&end_date={{ request('end_date') }}" class="btn-hero-secondary">
                    <i class="bi bi-file-earmark-excel"></i>CSV
                </a>
                <a href="{{ route('midwife.reports.export.pdf') }}?search={{ request('search') }}&status={{ request('status') }}&start_date={{ request('start_date') }}&end_date={{ request('end_date') }}" class="btn-hero-primary" target="_blank">
                    <i class="bi bi-file-earmark-pdf"></i>PDF
                </a>
            </div>
        </div>
    </div>

    <div class="metric-grid">
        <div class="metric-card metric-card-primary fade-in-card">
            <i class="bi bi-people-fill metric-card-icon"></i>
            <div class="metric-card-label">Total Patients</div>
            <div class="metric-card-value">{{ $totalPatients }}</div>
            <div class="metric-card-note">Patients included in the current reporting scope.</div>
        </div>
        <div class="metric-card metric-card-cyan fade-in-card">
            <i class="bi bi-heart-pulse-fill metric-card-icon"></i>
            <div class="metric-card-label">Pregnant Patients</div>
            <div class="metric-card-value">{{ $pregnantPatients }}</div>
            <div class="metric-card-note">Currently tracked maternal care cases.</div>
        </div>
        <div class="metric-card metric-card-green fade-in-card">
            <i class="bi bi-check-circle-fill metric-card-icon"></i>
            <div class="metric-card-label">Completed Checkups</div>
            <div class="metric-card-value">{{ $completedCheckups }}</div>
            <div class="metric-card-note">Finished visits across the filtered date range.</div>
        </div>
        <div class="metric-card metric-card-amber fade-in-card">
            <i class="bi bi-calendar2-week-fill metric-card-icon"></i>
            <div class="metric-card-label">Upcoming Appointments</div>
            <div class="metric-card-value">{{ $upcomingAppointments }}</div>
            <div class="metric-card-note">Scheduled follow-ups that still need attention.</div>
        </div>
    </div>

    <div class="workspace-panel fade-in-card">
        <div class="workspace-panel-header">
            <h2 class="workspace-panel-title"><i class="bi bi-funnel-fill"></i>Filter Reports</h2>
            <p class="workspace-panel-subtitle">Search by patient, maternal status, or report period.</p>
        </div>
        <div class="workspace-panel-body">
            <form method="GET" action="{{ route('midwife.reports.index') }}" class="workspace-filter-grid">
                <div class="span-4">
                    <label class="form-label">Search Patient</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by name" value="{{ $search }}">
                </div>
                <div class="span-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="all" {{ $status === 'all' || !$status ? 'selected' : '' }}>All</option>
                        <option value="pregnant" {{ $status === 'pregnant' ? 'selected' : '' }}>Pregnant</option>
                        <option value="postpartum" {{ $status === 'postpartum' ? 'selected' : '' }}>Postpartum</option>
                        <option value="not_pregnant" {{ $status === 'not_pregnant' ? 'selected' : '' }}>Not Pregnant</option>
                    </select>
                </div>
                <div class="span-2">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="span-2">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="span-2 workspace-filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Apply</button>
                    <a href="{{ route('midwife.reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i>Clear</a>
                </div>
            </form>

            <div class="section-chip-row mt-4">
                <a href="{{ route('midwife.reports.index', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d')]) }}" class="summary-chip chip-primary">This Month</a>
                <a href="{{ route('midwife.reports.index', ['start_date' => now()->startOfYear()->format('Y-m-d'), 'end_date' => now()->endOfYear()->format('Y-m-d')]) }}" class="summary-chip chip-info">This Year</a>
                <a href="{{ route('midwife.reports.index', ['status' => 'pregnant']) }}" class="summary-chip chip-warning">Pregnant Only</a>
            </div>
        </div>
    </div>

    <div class="workspace-panel fade-in-card">
        <div class="workspace-panel-header">
            <h2 class="workspace-panel-title"><i class="bi bi-table"></i>Patient Report List</h2>
            <p class="workspace-panel-subtitle">Open a patient report to view pregnancy history, checkups, and health records.</p>
        </div>
        <div class="workspace-panel-body pt-3">
            @if($patients->count() > 0)
                <div class="modern-table-wrap">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Age</th>
                                <th>Status</th>
                                <th>Last Checkup</th>
                                <th>Next Appointment</th>
                                <th>Assigned BHW</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $woman)
                                <tr>
                                    <td>
                                        <div class="table-title">{{ $woman->name }}</div>
                                        <div class="table-subtitle">{{ $woman->email }}</div>
                                    </td>
                                    <td>{{ $woman->age }}</td>
                                    <td>
                                        @if($woman->pregnancy_status === 'Pregnant')
                                            <span class="summary-chip chip-info">Pregnant</span>
                                        @elseif($woman->pregnancy_status === 'Postpartum')
                                            <span class="summary-chip chip-warning">Postpartum</span>
                                        @else
                                            <span class="summary-chip chip-success">Not Pregnant</span>
                                        @endif
                                    </td>
                                    <td>{{ $woman->last_checkup ? $woman->last_checkup->scheduled_date->format('M d, Y') : 'None' }}</td>
                                    <td>
                                        @if($woman->next_appointment)
                                            <span class="summary-chip chip-success">{{ $woman->next_appointment->scheduled_date->format('M d, Y') }}</span>
                                        @else
                                            <span class="table-subtitle">None scheduled</span>
                                        @endif
                                    </td>
                                    <td>{{ $woman->assigned_bhw?->name ?? 'Unassigned' }}</td>
                                    <td class="text-end">
                                        <div class="table-actions">
                                            <a href="{{ route('midwife.reports.details', $woman->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i>View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center pt-4">
                    {{ $patients->links() }}
                </div>
            @else
                <div class="empty-state-panel">
                    <i class="bi bi-inboxes"></i>
                    <h3>No patients found</h3>
                    <p>Try adjusting your search terms or widening the date range to see more records.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
