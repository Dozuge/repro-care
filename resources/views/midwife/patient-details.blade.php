@extends('midwife.layout')

@section('title', 'Woman Details - ReproCare')

@section('midwife-content')

{{-- ═══════════════════════════════
     PAGE HERO
═══════════════════════════════ --}}
<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-person-fill me-2"></i>{{ $woman->name }}
            </div>
            <p class="page-hero-subtitle">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, F j, Y') }}
                &nbsp;·&nbsp; Patient ID: #{{ $woman->id }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('midwife.patients') }}" class="btn-hero-primary">
                <i class="bi bi-arrow-left"></i> Back to Women
            </a>
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
     WOMAN INFORMATION CARD
═══════════════════════════════ --}}
<div class="card fade-in-card mb-4" style="border:none; background:var(--bg-card);">
    <div class="card-header" style="background:transparent; border-bottom:1px solid var(--border-color); padding:1rem 1.5rem;">
        <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text);">
            <i class="bi bi-person-fill me-2" style="color:var(--primary);"></i>
            Woman Information
        </h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--primary),var(--accent-violet));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-person" style="color:#fff;font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <small style="color:var(--text-muted); text-transform:uppercase; font-size:0.75rem; letter-spacing:0.5px;">Name</small>
                        <div style="font-weight:600; color:var(--text); font-size:1rem;">{{ $woman->name }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--info),var(--accent-cyan));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-envelope" style="color:#fff;font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <small style="color:var(--text-muted); text-transform:uppercase; font-size:0.75rem; letter-spacing:0.5px;">Email</small>
                        <div style="font-weight:500; color:var(--text); font-size:0.95rem;">{{ $woman->email }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--success),var(--primary));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-geo-alt" style="color:#fff;font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <small style="color:var(--text-muted); text-transform:uppercase; font-size:0.75rem; letter-spacing:0.5px;">Barangay</small>
                        <div style="font-weight:500; color:var(--text); font-size:0.95rem;">{{ $woman->barangay ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--success),var(--primary));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-shield-check" style="color:#fff;font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <small style="color:var(--text-muted); text-transform:uppercase; font-size:0.75rem; letter-spacing:0.5px;">Account Status</small>
                        <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.25rem 0.5rem; background:rgba(25,135,84,0.1); color:var(--success); border-radius:20px; font-weight:500; font-size:0.8rem; margin-top:0.25rem;">
                            <i class="bi bi-check-circle-fill"></i> Active
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--accent-rose),var(--primary));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-calendar-plus" style="color:#fff;font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <small style="color:var(--text-muted); text-transform:uppercase; font-size:0.75rem; letter-spacing:0.5px;">Member Since</small>
                        <div style="font-weight:500; color:var(--text); font-size:0.95rem;">{{ $woman->created_at ? $woman->created_at->format('M j, Y') : 'Unknown' }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--accent-amber),var(--warning));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-clock-history" style="color:#fff;font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <small style="color:var(--text-muted); text-transform:uppercase; font-size:0.75rem; letter-spacing:0.5px;">Last Login</small>
                        <div style="font-weight:500; color:var(--text); font-size:0.95rem;">{{ $woman->last_login_at ? $woman->last_login_at->format('M j, Y h:i A') : 'Never' }}</div>
                    </div>
                </div>
                @if($woman->recorded_by_bhw_id)
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--info),var(--accent-cyan));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-person-badge" style="color:#fff;font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <small style="color:var(--text-muted); text-transform:uppercase; font-size:0.75rem; letter-spacing:0.5px;">Recorded By</small>
                        <div style="font-weight:500; color:var(--text); font-size:0.95rem;">{{ $woman->recordedBy?->name ?? '—' }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════
     PARTNER INFORMATION CARD
═══════════════════════════════ --}}
@if($woman->partner_name || $woman->partner_contact)
<div class="card fade-in-card mb-4" style="border:none; background:var(--bg-card);">
    <div class="card-header" style="background:transparent; border-bottom:1px solid var(--border-color); padding:1rem 1.5rem;">
        <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text);">
            <i class="bi bi-people-fill me-2" style="color:var(--info);"></i>
            Partner / Spouse Information
        </h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="d-flex align-items-start gap-3">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--info),var(--accent-cyan));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-person-hearts" style="color:#fff;font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <small style="color:var(--text-muted); text-transform:uppercase; font-size:0.75rem; letter-spacing:0.5px;">Partner Name</small>
                        <div style="font-weight:600; color:var(--text); font-size:1rem;">{{ $woman->partner_name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-start gap-3">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--success),var(--primary));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-telephone-plus" style="color:#fff;font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <small style="color:var(--text-muted); text-transform:uppercase; font-size:0.75rem; letter-spacing:0.5px;">Partner Contact</small>
                        <div style="font-weight:500; color:var(--text); font-size:0.95rem;">
                            @if($woman->partner_contact)
                                <a href="tel:{{ $woman->partner_contact }}" style="color:var(--primary-light); text-decoration:none;">{{ $woman->partner_contact }}</a>
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ═══════════════════════════════
     EMERGENCY CONTACTS CARD
═══════════════════════════════ --}}
<div class="card fade-in-card mb-4" style="border:none; background:var(--bg-card);">
    <div class="card-header" style="background:transparent; border-bottom:1px solid var(--border-color); padding:1rem 1.5rem;">
        <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text);">
            <i class="bi bi-people-fill me-2" style="color:var(--accent-pink);"></i>
            Emergency Contact Information
        </h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            {{-- Primary Contact --}}
            <div class="col-md-6">
                <div class="p-3 h-100" style="background:rgba(255,255,255,0.01); border:1px solid var(--border-color); border-radius:14px;">
                    <h6 class="mb-3 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--primary-light);">
                        <i class="bi bi-1-circle-fill me-1"></i> Primary Contact
                    </h6>
                    @if($woman->primaryEmergencyContact)
                        <div class="d-flex flex-column gap-2" style="font-size:0.9rem;">
                            <div>
                                <small class="text-muted d-block" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Name</small>
                                <span class="fw-600 text-white">{{ $woman->primaryEmergencyContact->name }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Relationship</small>
                                <span class="fw-500 text-white">{{ $woman->primaryEmergencyContact->relationship }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Contact Number</small>
                                <a href="tel:{{ $woman->primaryEmergencyContact->contact_number }}" class="fw-500" style="color:var(--primary-light); text-decoration:none;">
                                    <i class="bi bi-telephone-fill me-1" style="font-size:0.8rem;"></i>{{ $woman->primaryEmergencyContact->contact_number }}
                                </a>
                            </div>
                            @if($woman->primaryEmergencyContact->address)
                            <div>
                                <small class="text-muted d-block" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Address</small>
                                <span class="fw-500 text-white-50">{{ $woman->primaryEmergencyContact->address }}</span>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-muted py-3" style="font-size:0.875rem;">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> No primary emergency contact recorded.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Secondary Contact --}}
            <div class="col-md-6">
                <div class="p-3 h-100" style="background:rgba(255,255,255,0.01); border:1px solid var(--border-color); border-radius:14px;">
                    <h6 class="mb-3 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-muted);">
                        <i class="bi bi-2-circle-fill me-1"></i> Secondary Contact (Optional)
                    </h6>
                    @if($woman->secondaryEmergencyContact)
                        <div class="d-flex flex-column gap-2" style="font-size:0.9rem;">
                            <div>
                                <small class="text-muted d-block" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Name</small>
                                <span class="fw-600 text-white">{{ $woman->secondaryEmergencyContact->name }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Relationship</small>
                                <span class="fw-500 text-white">{{ $woman->secondaryEmergencyContact->relationship }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Contact Number</small>
                                <a href="tel:{{ $woman->secondaryEmergencyContact->contact_number }}" class="fw-500" style="color:var(--text-muted); text-decoration:none;">
                                    <i class="bi bi-telephone-fill me-1" style="font-size:0.8rem;"></i>{{ $woman->secondaryEmergencyContact->contact_number }}
                                </a>
                            </div>
                            @if($woman->secondaryEmergencyContact->address)
                            <div>
                                <small class="text-muted d-block" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Address</small>
                                <span class="fw-500 text-white-50">{{ $woman->secondaryEmergencyContact->address }}</span>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-muted py-3" style="font-size:0.875rem;">
                            <i class="bi bi-info-circle me-1"></i> No secondary emergency contact recorded.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tertiary Contact --}}
            <div class="col-md-6">
                <div class="p-3 h-100" style="background:rgba(255,255,255,0.01); border:1px solid var(--border-color); border-radius:14px;">
                    <h6 class="mb-3 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-muted);">
                        <i class="bi bi-3-circle-fill me-1"></i> Tertiary Contact (Optional)
                    </h6>
                    @if($woman->tertiaryEmergencyContact)
                        <div class="d-flex flex-column gap-2" style="font-size:0.9rem;">
                            <div>
                                <small class="text-muted d-block" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Name</small>
                                <span class="fw-600 text-white">{{ $woman->tertiaryEmergencyContact->name }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Relationship</small>
                                <span class="fw-500 text-white">{{ $woman->tertiaryEmergencyContact->relationship }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Contact Number</small>
                                <a href="tel:{{ $woman->tertiaryEmergencyContact->contact_number }}" class="fw-500" style="color:var(--text-muted); text-decoration:none;">
                                    <i class="bi bi-telephone-fill me-1" style="font-size:0.8rem;"></i>{{ $woman->tertiaryEmergencyContact->contact_number }}
                                </a>
                            </div>
                            @if($woman->tertiaryEmergencyContact->address)
                            <div>
                                <small class="text-muted d-block" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Address</small>
                                <span class="fw-500 text-white-50">{{ $woman->tertiaryEmergencyContact->address }}</span>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-muted py-3" style="font-size:0.875rem;">
                            <i class="bi bi-info-circle me-1"></i> No tertiary emergency contact recorded.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════
     QUICK ACTIONS
═══════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <a href="{{ route('midwife.checkups.create', $woman->id) }}" 
           class="btn w-100 py-3 fade-in-card"
           style="background:linear-gradient(135deg,var(--success),var(--primary)); color:#fff; border:none; border-radius:12px; font-weight:500;">
            <i class="bi bi-calendar-check me-2"></i>Schedule Checkup
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('midwife.pregnancies.create', $woman->id) }}"
           class="btn w-100 py-3 fade-in-card" 
           style="background:linear-gradient(135deg,var(--secondary),var(--accent-pink)); color:#fff; border:none; border-radius:12px; font-weight:500;">
            <i class="bi bi-heart-pulse me-2"></i>Track Pregnancy
        </a>
    </div>
</div>

{{-- ═══════════════════════════════
     PREGNANCY HISTORY
═══════════════════════════════ --}}
<div class="card fade-in-card mb-4" style="border:none; background:var(--bg-card);">
    <div class="card-header d-flex justify-content-between align-items-center" 
         style="background:transparent; border-bottom:1px solid var(--border-color); padding:1rem 1.5rem;">
        <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text);">
            <i class="bi bi-heart-fill me-2" style="color:var(--success);"></i>
            Pregnancy History
        </h5>
    </div>
    <div class="card-body p-4">
        @if($pregnancies->count() > 0)
            @foreach($pregnancies as $pregnancy)
                @if($pregnancy->is_active)
                    {{-- Active Pregnancy Tracking Card --}}
                    <div class="mb-4 p-4" style="background:linear-gradient(135deg, rgba(13,202,240,0.1), rgba(102,16,242,0.05)); border:1px solid rgba(13,202,240,0.3); border-radius:16px;">
                        <div class="row align-items-center g-3">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <small style="color:var(--text-muted); text-transform:uppercase; font-size:0.7rem; letter-spacing:0.5px;">Current Gestational Age</small>
                                    <h4 style="font-weight:700; color:var(--text); margin:0.5rem 0;">{{ $pregnancy->formatted_aog }}</h4>
                                    <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.25rem 0.75rem; background:var(--info); color:#fff; border-radius:20px; font-size:0.8rem; font-weight:500;">
                                        {{ $pregnancy->trimester_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <small style="color:var(--text-muted); text-transform:uppercase; font-size:0.7rem; letter-spacing:0.5px;">Due Date</small>
                                    <h4 style="font-weight:700; color:var(--text); margin:0.5rem 0;">{{ $pregnancy->edd ? $pregnancy->edd->format('M j, Y') : 'N/A' }}</h4>
                                    <small style="color:var(--success);"><i class="bi bi-hourglass-split me-1"></i>{{ $pregnancy->days_until_due }} days remaining</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <small style="color:var(--text-muted); text-transform:uppercase; font-size:0.7rem; letter-spacing:0.5px;">Status</small>
                                    @if($pregnancy->is_overdue)
                                        <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.5rem 1rem; background:var(--danger); color:#fff; border-radius:20px; font-weight:600; margin:0.5rem 0;">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Overdue
                                        </div>
                                    @else
                                        <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.5rem 1rem; background:var(--success); color:#fff; border-radius:20px; font-weight:600; margin:0.5rem 0;">
                                            <i class="bi bi-heart-pulse-fill"></i> Active
                                        </div>
                                    @endif
                                    <small style="color:var(--text-muted); display:block;">Gravida: {{ $pregnancy->gravida }}, Para: {{ $pregnancy->para }}</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <a href="{{ route('midwife.pregnancies.show', $pregnancy->id) }}" 
                                       class="btn btn-sm" style="background:linear-gradient(135deg,var(--primary),var(--accent-violet)); color:#fff; border-radius:10px; padding:0.5rem 1rem;">
                                        <i class="bi bi-eye me-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
            
            <div class="table-responsive">
                <table class="table table-hover align-middle" style="font-size:0.9rem;">
                    <thead>
                        <tr style="color:var(--text-muted); font-weight:600; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.5px;">
                            <th style="padding:1rem; border:none;">LMP</th>
                            <th style="padding:1rem; border:none;">EDD</th>
                            <th style="padding:1rem; border:none;">AOG</th>
                            <th style="padding:1rem; border:none;">Gravida</th>
                            <th style="padding:1rem; border:none;">Para</th>
                            <th style="padding:1rem; border:none;">Status</th>
                            <th style="padding:1rem; border:none; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pregnancies as $pregnancy)
                            <tr style="border-bottom:1px solid var(--border-color);">
                                <td style="padding:1rem;">{{ $pregnancy->lmp ? $pregnancy->lmp->format('M j, Y') : 'N/A' }}</td>
                                <td style="padding:1rem;">{{ $pregnancy->edd ? $pregnancy->edd->format('M j, Y') : 'N/A' }}</td>
                                <td style="padding:1rem;">{{ $pregnancy->formatted_aog ?? 'N/A' }}</td>
                                <td style="padding:1rem;">{{ $pregnancy->gravida ?? 'N/A' }}</td>
                                <td style="padding:1rem;">{{ $pregnancy->para ?? 'N/A' }}</td>
                                <td style="padding:1rem;">
                                    @if($pregnancy->is_active ?? false)
                                        <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.35rem 0.75rem; background:rgba(25,135,84,0.1); color:var(--success); border-radius:20px; font-weight:500; font-size:0.85rem;">
                                            <i class="bi bi-heart-pulse-fill"></i> Active
                                        </div>
                                    @else
                                        <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.35rem 0.75rem; background:rgba(108,117,125,0.1); color:var(--text-muted); border-radius:20px; font-weight:500; font-size:0.85rem;">
                                            <i class="bi bi-check-circle-fill"></i> Completed
                                        </div>
                                    @endif
                                </td>
                                <td style="padding:1rem; text-align:right;">
                                    <a href="{{ route('midwife.pregnancies.show', $pregnancy->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       style="border-radius:8px; padding:0.4rem 0.6rem;">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <div style="width:60px;height:60px;border-radius:16px;background:linear-gradient(135deg,var(--primary),var(--accent-violet));display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="bi bi-heart" style="font-size:1.8rem;color:#fff;"></i>
                </div>
                <h5 style="font-weight:600; color:var(--text); margin-bottom:0.5rem;">No pregnancy records found</h5>
                <p style="color:var(--text-muted); margin-bottom:1rem;">Record the first pregnancy for this patient.</p>
                <a href="{{ route('midwife.pregnancies.create', $woman->id) }}" class="btn btn-primary" style="border-radius:10px;">
                    <i class="bi bi-plus me-1"></i> Record First Pregnancy
                </a>
            </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════
     CHECKUP HISTORY
═══════════════════════════════ --}}
<div class="card fade-in-card mb-4" style="border:none; background:var(--bg-card);">
    <div class="card-header d-flex justify-content-between align-items-center" 
         style="background:transparent; border-bottom:1px solid var(--border-color); padding:1rem 1.5rem;">
        <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text);">
            <i class="bi bi-calendar-check-fill me-2" style="color:var(--info);"></i>
            Checkup History
        </h5>
    </div>
    <div class="card-body p-4">
        @if($checkups->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle" style="font-size:0.9rem;">
                    <thead>
                        <tr style="color:var(--text-muted); font-weight:600; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.5px;">
                            <th style="padding:1rem; border:none;">Date</th>
                            <th style="padding:1rem; border:none;">Type</th>
                            <th style="padding:1rem; border:none;">Midwife</th>
                            <th style="padding:1rem; border:none;">Status</th>
                            <th style="padding:1rem; border:none;">Notes</th>
                            <th style="padding:1rem; border:none; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($checkups as $checkup)
                            <tr style="border-bottom:1px solid var(--border-color);">
                                <td style="padding:1rem; font-weight:500; color:var(--text);">{{ $checkup->scheduled_date->format('M j, Y') }}</td>
                                <td style="padding:1rem;">{{ $checkup->type ?? 'General' }}</td>
                                <td style="padding:1rem;">{{ $checkup->midwife->name ?? 'N/A' }}</td>
                                <td style="padding:1rem;">
                                    @switch($checkup->status)
                                        @case('scheduled')
                                            <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.35rem 0.75rem; background:rgba(255,193,7,0.1); color:var(--warning); border-radius:20px; font-weight:500; font-size:0.85rem;">
                                                <i class="bi bi-clock"></i> Scheduled
                                            </div>
                                            @break
                                        @case('completed')
                                            <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.35rem 0.75rem; background:rgba(25,135,84,0.1); color:var(--success); border-radius:20px; font-weight:500; font-size:0.85rem;">
                                                <i class="bi bi-check-circle-fill"></i> Completed
                                            </div>
                                            @break
                                        @case('missed')
                                            <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.35rem 0.75rem; background:rgba(220,53,69,0.1); color:var(--danger); border-radius:20px; font-weight:500; font-size:0.85rem;">
                                                <i class="bi bi-x-circle-fill"></i> Missed
                                            </div>
                                            @break
                                        @default
                                            <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.35rem 0.75rem; background:rgba(108,117,125,0.1); color:var(--text-muted); border-radius:20px; font-weight:500; font-size:0.85rem;">
                                                {{ $checkup->status }}
                                            </div>
                                    @endswitch
                                </td>
                                <td style="padding:1rem; color:var(--text-muted);">{{ \Illuminate\Support\Str::limit($checkup->notes ?? 'N/A', 50) }}</td>
                                <td style="padding:1rem; text-align:right;">
                                    <a href="{{ route('midwife.checkups.show', $checkup->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       style="border-radius:8px; padding:0.4rem 0.6rem;">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <div style="width:60px;height:60px;border-radius:16px;background:linear-gradient(135deg,var(--info),var(--accent-cyan));display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="bi bi-calendar-check" style="font-size:1.8rem;color:#fff;"></i>
                </div>
                <h5 style="font-weight:600; color:var(--text); margin-bottom:0.5rem;">No checkup records found</h5>
                <p style="color:var(--text-muted); margin-bottom:1rem;">Schedule the first checkup for this patient.</p>
                <a href="{{ route('midwife.checkups.create', $woman->id) }}" class="btn" style="background:var(--success); color:#fff; border-radius:10px;">
                    <i class="bi bi-plus me-1"></i> Schedule First Checkup
                </a>
            </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════
     HEALTH RECORDS
═══════════════════════════════ --}}
<div class="card fade-in-card" style="border:none; background:var(--bg-card);">
    <div class="card-header d-flex justify-content-between align-items-center" 
         style="background:transparent; border-bottom:1px solid var(--border-color); padding:1rem 1.5rem;">
        <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text);">
            <i class="bi bi-clipboard-pulse-fill me-2" style="color:var(--warning);"></i>
            Health Records
        </h5>
    </div>
    <div class="card-body p-4">
        @if($healthRecords->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle" style="font-size:0.9rem;">
                    <thead>
                        <tr style="color:var(--text-muted); font-weight:600; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.5px;">
                            <th style="padding:1rem; border:none;">Date</th>
                            <th style="padding:1rem; border:none;">Type</th>
                            <th style="padding:1rem; border:none;">Recorded By</th>
                            <th style="padding:1rem; border:none;">Summary</th>
                            <th style="padding:1rem; border:none; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($healthRecords as $record)
                            <tr style="border-bottom:1px solid var(--border-color);">
                                <td style="padding:1rem; font-weight:500; color:var(--text);">{{ $record->created_at->format('M j, Y') }}</td>
                                <td style="padding:1rem;">{{ $record->type ?? 'General' }}</td>
                                <td style="padding:1rem;">
                                    @if($record->recordedBy)
                                        <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.35rem 0.75rem; background:rgba(102,16,242,0.1); color:var(--primary); border-radius:20px; font-weight:500; font-size:0.85rem;">
                                            <i class="bi bi-person-badge"></i> {{ $record->recordedBy->name }}
                                        </div>
                                    @else
                                        <span style="color:var(--text-muted);">N/A</span>
                                    @endif
                                </td>
                                <td style="padding:1rem; color:var(--text-muted);">{{ \Illuminate\Support\Str::limit($record->notes ?? 'N/A', 50) }}</td>
                                <td style="padding:1rem; text-align:right;">
                                    <a href="{{ route('midwife.health-records.show', $record->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       style="border-radius:8px; padding:0.4rem 0.6rem;">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <div style="width:60px;height:60px;border-radius:16px;background:linear-gradient(135deg,var(--warning),var(--accent-amber));display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="bi bi-clipboard-pulse" style="font-size:1.8rem;color:#fff;"></i>
                </div>
                <h5 style="font-weight:600; color:var(--text); margin-bottom:0.5rem;">No health records found</h5>
                <p style="color:var(--text-muted); margin-bottom:1rem;">This patient has no health records yet.</p>
            </div>
        @endif
    </div>
</div>

@endsection
