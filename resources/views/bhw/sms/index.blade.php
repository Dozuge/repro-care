@extends('bhw.layout')

@section('title', 'SMS Alerts Dashboard | ReproCare')

@section('bhw-content')

<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-chat-dots-fill me-2" style="color:var(--primary-light);"></i>SMS Alert Management
            </div>
            <p class="page-hero-subtitle">
                Send health alerts, appointment reminders, and broadcast messages to patients via SMS.
            </p>
        </div>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card fade-in-card p-3 text-center" style="border-radius:14px;">
            <div class="text-muted" style="font-size:.75rem;">Total SMS Sent</div>
            <h3 class="fw-800 mb-0 mt-1" style="color:var(--primary-light);">{{ $stats['total'] }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card fade-in-card p-3 text-center" style="border-radius:14px;">
            <div class="text-muted" style="font-size:.75rem;">Successful</div>
            <h3 class="fw-800 text-success mb-0 mt-1">{{ $stats['sent'] }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card fade-in-card p-3 text-center" style="border-radius:14px;">
            <div class="text-muted" style="font-size:.75rem;">Failed</div>
            <h3 class="fw-800 text-danger mb-0 mt-1">{{ $stats['failed'] }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card fade-in-card p-3 text-center" style="border-radius:14px;">
            <div class="text-muted" style="font-size:.75rem;">SMS-Enabled Patients</div>
            <h3 class="fw-800 text-info mb-0 mt-1">{{ $stats['enabled'] }}</h3>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- Left: Send Forms --}}
    <div class="col-lg-5">

        {{-- Send to Individual Patient --}}
        <div class="card fade-in-card mb-4">
            <div class="card-header">
                <h5 class="mb-0 fw-700">
                    <i class="bi bi-person-fill me-2" style="color:var(--primary-light);"></i>Send to Patient
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('bhw.sms.send') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-xs text-muted fw-600">Select Patient</label>
                        <select name="user_id" class="form-select" required id="patientSelect">
                            <option value="">— Choose patient —</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}">{{ trim($p->first_name . ' ' . ($p->middle_initial ? $p->middle_initial . '. ' : '') . $p->last_name) }} – {{ $p->contact_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs text-muted fw-600">Message (English)</label>
                        <textarea name="message_en" class="form-control" rows="3" required maxlength="320"
                            placeholder="Type your message in English..."></textarea>
                        <div class="text-muted" style="font-size:0.7rem; text-align:right;">Max 320 characters</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs text-muted fw-600">Message (Tagalog) <span class="text-muted">(optional)</span></label>
                        <textarea name="message_tl" class="form-control" rows="3" maxlength="320"
                            placeholder="Isulat ang mensahe sa Tagalog..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-send-fill me-2"></i>Send SMS
                    </button>
                </form>
            </div>
        </div>

        {{-- Broadcast --}}
        <div class="card fade-in-card">
            <div class="card-header">
                <h5 class="mb-0 fw-700">
                    <i class="bi bi-megaphone-fill me-2" style="color:#f59e0b;"></i>Broadcast to Group
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('bhw.sms.broadcast') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-xs text-muted fw-600">Filter by Purok <span class="text-muted">(optional)</span></label>
                        <select name="purok_id" class="form-select">
                            <option value="">— All Puroks —</option>
                            @foreach($puroks as $purok)
                                <option value="{{ $purok->id }}">{{ $purok->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs text-muted fw-600">Message (English)</label>
                        <textarea name="message_en" class="form-control" rows="3" required maxlength="320"
                            placeholder="Broadcast message in English..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs text-muted fw-600">Message (Tagalog) <span class="text-muted">(optional)</span></label>
                        <textarea name="message_tl" class="form-control" rows="3" maxlength="320"
                            placeholder="Mensahe sa Tagalog..."></textarea>
                    </div>
                    <div class="alert alert-warning py-2 mb-3" style="font-size:.78rem;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        This will send SMS to up to <strong>{{ $stats['enabled'] }}</strong> opted-in patients. This action cannot be undone.
                    </div>
                    <button type="submit" class="btn btn-warning w-100" style="color:#000;"
                        onclick="return confirm('Send broadcast SMS to {{ $stats['enabled'] }} patient(s)?')">
                        <i class="bi bi-megaphone-fill me-2"></i>Send Broadcast
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Right: SMS Log Table --}}
    <div class="col-lg-7">
        <div class="card fade-in-card">
            <div class="card-header d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <h5 class="mb-0 fw-700">
                    <i class="bi bi-clock-history me-2" style="color:var(--primary-light);"></i>SMS Log
                </h5>
                {{-- Filters --}}
                <form method="GET" class="d-flex gap-2 flex-wrap">
                    <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                    <select name="type" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="appointment_reminder" {{ request('type') === 'appointment_reminder' ? 'selected' : '' }}>Appointment</option>
                        <option value="high_risk_alert" {{ request('type') === 'high_risk_alert' ? 'selected' : '' }}>High Risk</option>
                        <option value="missed_checkup" {{ request('type') === 'missed_checkup' ? 'selected' : '' }}>Missed Checkup</option>
                        <option value="custom" {{ request('type') === 'custom' ? 'selected' : '' }}>Custom</option>
                        <option value="broadcast" {{ request('type') === 'broadcast' ? 'selected' : '' }}>Broadcast</option>
                    </select>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:.82rem;">
                        <thead>
                            <tr style="background:rgba(255,255,255,0.03);">
                                <th class="px-3 py-2">Patient</th>
                                <th class="py-2">Type</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Sent At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr class="border-top" style="border-color:rgba(255,255,255,0.06) !important;">
                                    <td class="px-3 py-2">
                                        @if($log->user)
                                            <span class="fw-600">{{ $log->user->name }}</span><br>
                                        @endif
                                        <span class="text-muted">{{ $log->phone_number }}</span>
                                    </td>
                                    <td class="py-2">
                                        <span style="font-size:1rem;" title="{{ $log->type }}">{{ $log->type_icon }}</span>
                                        <span class="text-muted" style="font-size:.75rem;">{{ str_replace('_', ' ', ucfirst($log->type)) }}</span>
                                    </td>
                                    <td class="py-2">{!! $log->status_badge !!}</td>
                                    <td class="py-2 text-muted">
                                        {{ $log->sent_at ? $log->sent_at->format('M j, Y g:i A') : ($log->created_at->format('M j, Y g:i A')) }}
                                        @if($log->error_message)
                                            <br><span class="text-danger" style="font-size:.7rem;" title="{{ $log->error_message }}">
                                                <i class="bi bi-info-circle me-1"></i>Error
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-chat-dots" style="font-size:2rem;"></i>
                                        <p class="mt-2">No SMS messages sent yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($logs->hasPages())
                    <div class="px-3 py-2">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
