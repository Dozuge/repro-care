@extends('midwife.layout')

@section('title', 'Checkup Referrals - ReproCare')

@section('midwife-content')
<div class="py-4">
    <div class="page-hero fade-in-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
            <div>
                <h1 class="page-hero-title"><i class="bi bi-clipboard2-heart-fill me-2"></i>Checkup Referrals</h1>
                <p class="page-hero-subtitle">Accept a referral to create a checkup schedule right away.</p>
            </div>
            <div class="btn-hero-secondary">
                <i class="bi bi-inbox-fill me-1"></i> {{ $referrals->total() }} referrals
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success fade-in-card">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm fade-in-card" style="background:var(--bg-card);">
        <div class="card-body p-0">
            @if($referrals->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Patient</th>
                                <th>Referred By</th>
                                <th>Reason</th>
                                <th>Urgency</th>
                                <th>Date</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($referrals as $referral)
                                @php
                                    $isWalkIn = (bool) $referral->walk_in_patient_id;
                                    $patient = $isWalkIn ? $referral->walkInPatient : $referral->woman;
                                    $name = $isWalkIn ? ($patient->full_name ?? $referral->patient_name) : ($patient->name ?? $referral->patient_name);
                                    $subtitle = $isWalkIn ? ($patient->contact_number ?? 'Walk-in woman') : ($patient->email ?? 'Registered woman');
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--accent-violet));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;">
                                                {{ strtoupper(substr($name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold" style="color:var(--text);">{{ $name }}</div>
                                                <small style="color:var(--text-muted);">{{ $subtitle }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $referral->referredByBhw?->name ?? 'N/A' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($referral->reason, 70) }}</td>
                                    <td>
                                        <span class="badge {{ $referral->urgency === 'emergency' ? 'bg-danger' : ($referral->urgency === 'urgent' ? 'bg-warning text-dark' : 'bg-info') }}">
                                            {{ ucfirst($referral->urgency) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ $referral->created_at->format('M j, Y') }}</div>
                                        <small class="text-muted">{{ $referral->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('midwife.referrals.show', $referral->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $referrals->links() }}</div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-clipboard2-heart" style="font-size:2.5rem;"></i>
                    <div class="mt-3">No referrals are waiting for review.</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
