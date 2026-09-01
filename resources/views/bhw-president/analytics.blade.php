@extends('bhw-president.layout')

@section('title', 'Analytics - BHW President Portal | ReproCare')

@section('bhw-president-content')

<div class="page-hero fade-in-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">Maternal Health Analytics</div>
            <p class="page-hero-subtitle">Comprehensive health indicators and statistics</p>
        </div>
    </div>
</div>

{{-- Maternal Health Indicators --}}
<div class="card fade-in-card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-heart-pulse me-2" style="color:var(--accent-rose);"></i>Maternal Health Indicators</h5>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card stat-purple fade-in-card">
                    <div class="stat-icon"><i class="bi bi-heart-fill"></i></div>
                    <div class="stat-label">Total Pregnancies</div>
                    <div class="stat-number">{{ $maternalStats['totalPregnancies'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-cyan fade-in-card">
                    <div class="stat-icon"><i class="bi bi-heart-pulse-fill"></i></div>
                    <div class="stat-label">Active Pregnancies</div>
                    <div class="stat-number">{{ $maternalStats['activePregnancies'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-green fade-in-card">
                    <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                    <div class="stat-label">Completed</div>
                    <div class="stat-number">{{ $maternalStats['completedPregnancies'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-amber fade-in-card">
                    <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div class="stat-label">High Risk</div>
                    <div class="stat-number">{{ $maternalStats['highRiskCount'] }}</div>
                    <div class="stat-trend">{{ $maternalStats['highRiskPercentage'] }}% of active</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Service Utilization --}}
<div class="card fade-in-card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-graph-up me-2" style="color:var(--success);"></i>Service Utilization</h5>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card stat-purple fade-in-card">
                    <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
                    <div class="stat-label">Checkups Completed</div>
                    <div class="stat-number">{{ $serviceStats['checkupsCompleted'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-rose fade-in-card">
                    <div class="stat-icon"><i class="bi bi-calendar-x-fill"></i></div>
                    <div class="stat-label">Checkups Missed</div>
                    <div class="stat-number">{{ $serviceStats['checkupsMissed'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-cyan fade-in-card">
                    <div class="stat-icon"><i class="bi bi-file-medical-fill"></i></div>
                    <div class="stat-label">Health Records</div>
                    <div class="stat-number">{{ $serviceStats['healthRecordsTotal'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-green fade-in-card">
                    <div class="stat-icon"><i class="bi bi-calculator"></i></div>
                    <div class="stat-label">Avg Checkups/Patient</div>
                    <div class="stat-number">{{ $serviceStats['averageCheckupsPerPatient'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card fade-in-card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar-month me-2" style="color:var(--accent-violet);"></i>Monthly Trends (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Pregnancies</th>
                                <th>Checkups</th>
                                <th>Health Records</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyTrends as $trend)
                            <tr>
                                <td>{{ $trend['month'] }}</td>
                                <td>{{ $trend['pregnancies'] }}</td>
                                <td>{{ $trend['checkups'] }}</td>
                                <td>{{ $trend['healthRecords'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
