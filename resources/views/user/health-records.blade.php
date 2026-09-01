@extends('user.layout')

@section('title', 'My Health Records - ReproCare')

@section('user-content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-file-medical"></i> My Health Records</h1>
    </div>
    
    <div class="row">
        @if($healthRecords->count() > 0)
            @foreach($healthRecords as $record)
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge {{ $record->risk_level === 'Low' ? 'bg-success' : ($record->risk_level === 'Medium' ? 'bg-warning' : 'bg-danger') }}">
                                {{ $record->risk_level }} Risk
                            </span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Health Check</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Blood Pressure:</strong><br>{{ $record->bp }}</p>
                                    <p><strong>Weight:</strong><br>{{ $record->weight }} kg</p>
                                    <p><strong>Heart Rate:</strong><br>{{ $record->heart_rate }} bpm</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Temperature:</strong><br>{{ $record->temperature }}°C</p>
                                    @if($record->recordedBy)
                                        <p><strong>Recorded by:</strong><br>{{ optional($record->recordedBy)->name ?? 'N/A' }} ({{ ucfirst($record->recorded_by_role ?? 'N/A') }})</p>
                                    @endif
                                </div>
                            </div>
                            @if($record->notes)
                                <div class="mt-3">
                                    <p><strong>Notes:</strong><br>{{ $record->notes }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">Recorded on {{ $record->created_at->format('M j, Y g:i A') }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-file-medical text-muted" style="font-size: 4rem;"></i>
                    <h3 class="text-muted mt-3">No health records</h3>
                    <p class="text-muted">Your health records will appear here once recorded by your healthcare provider.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
