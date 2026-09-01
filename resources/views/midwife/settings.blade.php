@extends('midwife.layout')

@section('title', 'Settings - Midwife Portal | ReproCare')

@push('styles')
<style>
    /* ── Settings Layout ── */
    .settings-wrap         { display: flex; gap: 1.5rem; align-items: flex-start; }
    .settings-sidebar      { width: 240px; flex-shrink: 0; position: sticky; top: 80px; }
    .settings-content      { flex: 1; min-width: 0; }

    /* Settings nav */
    .settings-nav          { background: var(--bg-card); border: 1px solid var(--border); border-radius: 18px; overflow: hidden; padding: 0.5rem; }
    .settings-nav-item     { display: flex; align-items: center; gap: 0.75rem; padding: 0.7rem 0.9rem; border-radius: 12px; font-size: 0.875rem; font-weight: 500; color: var(--text-muted); cursor: pointer; text-decoration: none; transition: all 0.2s ease; border: 1px solid transparent; margin-bottom: 0.15rem; }
    .settings-nav-item i   { font-size: 1rem; width: 1.15rem; text-align: center; flex-shrink: 0; }
    .settings-nav-item:hover { background: var(--primary-subtle); color: var(--text); }
    .settings-nav-item.active { background: linear-gradient(135deg, var(--primary-subtle), rgba(155,54,255,0.10)); color: var(--primary-light); border-color: var(--border-glass); font-weight: 600; }

    /* Settings sections */
    .settings-section      { display: none; }
    .settings-section.active { display: block; }

    /* Pref rows */
    .pref-card             { background: var(--bg-card); border: 1px solid var(--border); border-radius: 18px; margin-bottom: 1.25rem; overflow: hidden; transition: background 0.4s ease, border-color 0.3s ease; }
    .pref-card-header      { padding: 1.1rem 1.4rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 0.75rem; }
    .pref-card-header-icon { width: 38px; height: 38px; border-radius: 11px; background: var(--primary-subtle); border: 1px solid var(--border-glass); display: flex; align-items: center; justify-content: center; font-size: 1rem; color: var(--primary-light); flex-shrink: 0; }
    .pref-card-header h6   { margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; font-size: 0.95rem; color: var(--text); }
    .pref-card-header p    { margin: 0; font-size: 0.78rem; color: var(--text-muted); }
    .pref-card-body        { padding: 1.25rem 1.4rem; }

    /* Pref row (toggle list item) */
    .pref-row { display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 0; border-bottom: 1px solid var(--border); }
    .pref-row:last-child { border-bottom: none; padding-bottom: 0; }
    .pref-row-label h6 { margin: 0 0 0.15rem; font-size: 0.9rem; font-weight: 600; color: var(--text); }
    .pref-row-label p  { margin: 0; font-size: 0.8rem; color: var(--text-muted); }

    /* Theme option cards */
    .theme-option-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem; }
    .theme-option {
        border: 2px solid var(--border);
        border-radius: 14px;
        padding: 1rem;
        cursor: pointer;
        text-align: center;
        transition: all 0.25s ease;
        position: relative;
        background: var(--bg-card2);
    }
    .theme-option:hover { border-color: var(--primary); transform: translateY(-2px); }
    .theme-option.active { border-color: var(--primary); background: var(--primary-subtle); }

    .theme-preview {
        border-radius: 10px;
        overflow: hidden;
        height: 70px;
        display: flex;
        margin-bottom: 0.75rem;
        border: 1px solid var(--border);
    }
    .preview-sidebar { width: 28%; background: #1a0d2e; }
    .preview-body { flex: 1; padding: 0.4rem; display: flex; flex-direction: column; gap: 0.25rem; background: #08040f; }
    .preview-card-sm { height: 14px; background: #1d0f33; border-radius: 4px; }
    .preview-card-sm.wide { width: 100%; }
    .preview-card-sm.half { width: 60%; }

    .light-preview .preview-sidebar { background: #e9d5ff; }
    .light-preview .preview-body    { background: #f6f0ff; }
    .light-preview .preview-card-sm { background: #ffffff; }

    .theme-option-label { font-size: 0.85rem; font-weight: 600; color: var(--text); }
    .theme-check { position: absolute; top: 0.6rem; right: 0.6rem; font-size: 1rem; color: var(--primary); display: none; }
    .theme-option.active .theme-check { display: inline-block; }

    /* Toggle quick row */
    .theme-toggle-row { display: flex; align-items: center; gap: 0.9rem; margin-top: 1.25rem; padding: 0.85rem 1rem; background: var(--bg-card2); border: 1px solid var(--border); border-radius: 12px; }
    .theme-toggle-row span { font-size: 0.875rem; color: var(--text-muted); font-weight: 500; display: flex; align-items: center; gap: 0.4rem; }

    /* Danger zone */
    .danger-zone { border: 1px solid rgba(239,68,68,0.25); border-radius: 16px; padding: 1.25rem 1.4rem; background: rgba(239,68,68,0.05); }
    .danger-zone h6 { color: #f87171; font-weight: 700; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem; }

    /* Session badge */
    .session-badge { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.25rem 0.7rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); border-radius: 20px; font-size: 0.78rem; font-weight: 600; color: #34d399; }

    /* Page title row */
    .settings-page-header { margin-bottom: 1.75rem; }
    .settings-page-header h1 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.5rem; font-weight: 800; color: var(--text); margin-bottom: 0.2rem; }
    .settings-page-header p  { font-size: 0.875rem; color: var(--text-muted); margin: 0; }

    .location-map-frame {
        width: 100%;
        height: 280px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background:
            linear-gradient(135deg, rgba(6, 182, 212, 0.10), rgba(155, 54, 255, 0.10)),
            var(--bg-card);
        display: block;
        position: relative;
        overflow: hidden;
    }

    .location-map-svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    .location-map-badge {
        position: absolute;
        top: 0.9rem;
        left: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.7rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid rgba(255, 255, 255, 0.95);
        color: #3b2b57;
        font-size: 0.78rem;
        font-weight: 700;
        box-shadow: 0 8px 22px rgba(41, 18, 74, 0.12);
    }

    .location-map-badge i {
        color: #ef4444;
    }

    .location-map-meta {
        position: absolute;
        right: 0.9rem;
        bottom: 0.9rem;
        max-width: 250px;
        padding: 0.7rem 0.8rem;
        border-radius: 14px;
        background: rgba(19, 10, 34, 0.84);
        border: 1px solid rgba(218, 54, 255, 0.18);
        color: #f8f5ff;
        backdrop-filter: blur(8px);
    }

    .location-map-meta strong {
        display: block;
        font-size: 0.82rem;
        margin-bottom: 0.2rem;
    }

    .location-map-meta span {
        display: block;
        font-size: 0.74rem;
        color: rgba(248, 245, 255, 0.8);
    }

    @media (max-width: 768px) {
        .settings-wrap { flex-direction: column; }
        .settings-sidebar { width: 100%; position: static; }
        .theme-option-grid { grid-template-columns: 1fr 1fr; }
    }
</style>
@endpush

@section('midwife-content')

{{-- Page Header --}}
<div class="settings-page-header fade-in-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-gear-fill me-2" style="color:var(--primary-light);"></i>Settings</h1>
            <p>Manage your account preferences and system settings</p>
        </div>
        <a href="{{ route('profile.show') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-person-circle me-1"></i> Back to Profile
        </a>
    </div>
</div>

<div class="settings-wrap">

    {{-- ── LEFT NAV ── --}}
    <div class="settings-sidebar fade-in-card">
        <div class="settings-nav">
            <a class="settings-nav-item active" onclick="showSection('appearance', this)" href="#">
                <i class="bi bi-palette-fill" style="color:var(--primary-light);"></i>
                Appearance
            </a>
            <a class="settings-nav-item" onclick="showSection('account', this)" href="#">
                <i class="bi bi-person-fill" style="color:var(--info);"></i>
                Account
            </a>
            <a class="settings-nav-item" onclick="showSection('security', this)" href="#">
                <i class="bi bi-shield-lock-fill" style="color:var(--warning);"></i>
                Security
            </a>
            <a class="settings-nav-item" onclick="showSection('notifications', this)" href="#">
                <i class="bi bi-bell-fill" style="color:var(--success);"></i>
                Notifications
            </a>
            <a class="settings-nav-item" onclick="showSection('privacy', this)" href="#">
                <i class="bi bi-lock-fill" style="color:var(--danger);"></i>
                Privacy
            </a>
        </div>
    </div>

    {{-- ── RIGHT CONTENT ── --}}
    <div class="settings-content">

        {{-- ══ APPEARANCE ══ --}}
        <div class="settings-section active" id="section-appearance">

            {{-- Theme Selector --}}
            <div class="pref-card fade-in-card">
                <div class="pref-card-header">
                    <div class="pref-card-header-icon"><i class="bi bi-palette-fill"></i></div>
                    <div>
                        <h6>Color Theme</h6>
                        <p>Choose your preferred interface appearance</p>
                    </div>
                </div>
                <div class="pref-card-body">
                    <div class="theme-option-grid">
                        {{-- Light --}}
                        <div class="theme-option" data-theme="light" id="theme-opt-light">
                            <div class="theme-preview light-preview">
                                <div class="preview-sidebar"></div>
                                <div class="preview-body">
                                    <div class="preview-card-sm wide"></div>
                                    <div class="preview-card-sm wide"></div>
                                    <div class="preview-card-sm half"></div>
                                </div>
                            </div>
                            <div class="theme-option-label">☀️ Light Mode</div>
                            <i class="bi bi-check-circle-fill theme-check" id="check-light"></i>
                        </div>
                        {{-- Dark --}}
                        <div class="theme-option" data-theme="dark" id="theme-opt-dark">
                            <div class="theme-preview dark-preview">
                                <div class="preview-sidebar"></div>
                                <div class="preview-body">
                                    <div class="preview-card-sm wide"></div>
                                    <div class="preview-card-sm wide"></div>
                                    <div class="preview-card-sm half"></div>
                                </div>
                            </div>
                            <div class="theme-option-label">🌙 Dark Mode</div>
                            <i class="bi bi-check-circle-fill theme-check" id="check-dark"></i>
                        </div>
                    </div>

                    {{-- Quick toggle --}}
                    <div class="theme-toggle-row">
                        <span><i class="bi bi-sun-fill" style="color:var(--warning);"></i> Light</span>
                        <label class="rc-switch mb-0 mx-auto">
                            <input type="checkbox" class="theme-switch-input">
                            <span class="rc-track"><span class="rc-thumb"></span></span>
                        </label>
                        <span><i class="bi bi-moon-stars-fill" style="color:var(--accent-violet);"></i> Dark</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ══ ACCOUNT ══ --}}
        <div class="settings-section" id="section-account">
            <div class="pref-card fade-in-card">
                <div class="pref-card-header">
                    <div class="pref-card-header-icon"><i class="bi bi-person-fill"></i></div>
                    <div>
                        <h6>Account Information</h6>
                        <p>Update your personal details</p>
                    </div>
                </div>
                <div class="pref-card-body">
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" value="{{ auth()->user()->email }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" value="{{ auth()->user()->phone ?? '' }}" placeholder="Enter phone number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" value="{{ auth()->user()->date_of_birth ?? '' }}" readonly>
                            </div>
                            <div class="col-12">
                                <div style="border:1px solid var(--border); border-radius:16px; overflow:hidden; background:var(--bg-card2);">
                                    <div style="padding:1rem 1rem 0.75rem; border-bottom:1px solid var(--border);">
                                        <div style="font-size:0.92rem; font-weight:700; color:var(--text);">
                                            <i class="bi bi-geo-alt-fill me-2" style="color:var(--info);"></i>Facility Location
                                        </div>
                                        <div style="font-size:0.82rem; color:var(--text-muted); margin-top:0.25rem;">
                                            Barangay Burgos Padlan, San Carlos City, Pangasinan
                                        </div>
                                    </div>
                                    <div style="padding:1rem;">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Barangay</label>
                                                <input type="text" class="form-control" value="{{ auth()->user()->barangay ?? 'Not assigned' }}" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">City</label>
                                                <input type="text" class="form-control" value="San Carlos City" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Province</label>
                                                <input type="text" class="form-control" value="Pangasinan" readonly>
                                            </div>
                                            <div class="col-12">
                                                <div class="location-map-frame" role="img" aria-label="Map of Barangay Burgos Padlan, San Carlos City, Pangasinan">
                                                    <svg class="location-map-svg" viewBox="0 0 900 280" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                                                        <defs>
                                                            <linearGradient id="mapBg" x1="0%" y1="0%" x2="100%" y2="100%">
                                                                <stop offset="0%" stop-color="#dff4ff" />
                                                                <stop offset="55%" stop-color="#efe7ff" />
                                                                <stop offset="100%" stop-color="#ffe8f4" />
                                                            </linearGradient>
                                                            <pattern id="mapGrid" width="54" height="54" patternUnits="userSpaceOnUse">
                                                                <path d="M 54 0 L 0 0 0 54" fill="none" stroke="rgba(112, 85, 151, 0.10)" stroke-width="1"/>
                                                            </pattern>
                                                            <filter id="pinShadow" x="-50%" y="-50%" width="200%" height="200%">
                                                                <feDropShadow dx="0" dy="6" stdDeviation="6" flood-color="rgba(80, 31, 120, 0.28)"/>
                                                            </filter>
                                                        </defs>

                                                        <rect width="900" height="280" fill="url(#mapBg)"/>
                                                        <rect width="900" height="280" fill="url(#mapGrid)"/>

                                                        <path d="M-40 214 C110 190, 170 235, 302 207 S520 145, 660 170 S825 222, 940 180" fill="none" stroke="#ffffff" stroke-width="34" stroke-linecap="round" opacity="0.96"/>
                                                        <path d="M-40 214 C110 190, 170 235, 302 207 S520 145, 660 170 S825 222, 940 180" fill="none" stroke="#c9b8ee" stroke-width="3" stroke-linecap="round" stroke-dasharray="10 12" opacity="0.8"/>

                                                        <path d="M118 -10 C160 36, 220 72, 250 122 S285 236, 336 300" fill="none" stroke="#fffdfd" stroke-width="24" stroke-linecap="round" opacity="0.95"/>
                                                        <path d="M118 -10 C160 36, 220 72, 250 122 S285 236, 336 300" fill="none" stroke="#d5c7f3" stroke-width="2" stroke-linecap="round" stroke-dasharray="7 9" opacity="0.85"/>

                                                        <path d="M530 4 C558 56, 552 100, 590 142 S708 212, 762 286" fill="none" stroke="#fffdfd" stroke-width="18" stroke-linecap="round" opacity="0.92"/>
                                                        <path d="M530 4 C558 56, 552 100, 590 142 S708 212, 762 286" fill="none" stroke="#d9cdf4" stroke-width="2" stroke-linecap="round" stroke-dasharray="6 8" opacity="0.82"/>

                                                        <path d="M370 52 C408 70, 448 82, 480 110 S526 164, 592 184" fill="none" stroke="#8dd3c7" stroke-width="34" stroke-linecap="round" opacity="0.38"/>

                                                        <circle cx="468" cy="138" r="22" fill="#ffffff" opacity="0.92"/>
                                                        <path d="M468 90 C444 90, 425 109, 425 133 C425 168, 468 213, 468 213 C468 213, 511 168, 511 133 C511 109, 492 90, 468 90 Z" fill="#ef4444" filter="url(#pinShadow)"/>
                                                        <circle cx="468" cy="133" r="15" fill="#ffffff"/>
                                                        <circle cx="468" cy="133" r="6" fill="#ef4444"/>

                                                        <text x="500" y="118" font-size="20" font-weight="700" fill="#35214d">Burgos Padlan</text>
                                                        <text x="500" y="142" font-size="13" fill="#6d5b8d">San Carlos City, Pangasinan</text>
                                                        <text x="500" y="162" font-size="12" fill="#8b7aa8">Approx. coordinates: 15.92806, 120.3478</text>
                                                    </svg>

                                                    <div class="location-map-badge">
                                                        <i class="bi bi-geo-alt-fill"></i>
                                                        Coverage Map
                                                    </div>

                                                    <div class="location-map-meta">
                                                        <strong>Barangay Burgos Padlan</strong>
                                                        <span>San Carlos City, Pangasinan</span>
                                                        <span>15.92806, 120.3478</span>
                                                    </div>
                                                </div>
                                                <div class="mt-2" style="font-size:0.8rem; color:var(--text-muted);">
                                                    Open the exact location in <a href="https://www.google.com/maps/search/?api=1&query=Barangay+Burgos+Padlan+San+Carlos+City+Pangasinan" target="_blank" rel="noopener noreferrer">Google Maps</a>.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 d-flex gap-2 pt-1">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i> Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ══ SECURITY ══ --}}
        <div class="settings-section" id="section-security">

            {{-- Change Password --}}
            <div class="pref-card fade-in-card">
                <div class="pref-card-header">
                    <div class="pref-card-header-icon" style="background:rgba(245,158,11,0.12);color:var(--warning);">
                        <i class="bi bi-key-fill"></i>
                    </div>
                    <div>
                        <h6>Change Password</h6>
                        <p>Update your login password</p>
                    </div>
                </div>
                <div class="pref-card-body">
                    <form class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" placeholder="Enter current password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" placeholder="Min. 8 characters">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password" placeholder="Repeat new password">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-shield-lock me-1"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Two-Factor & Sessions --}}
            <div class="pref-card fade-in-card">
                <div class="pref-card-header">
                    <div class="pref-card-header-icon" style="background:rgba(16,185,129,0.12);color:var(--success);">
                        <i class="bi bi-shield-fill-check"></i>
                    </div>
                    <div>
                        <h6>Security Options</h6>
                        <p>Extra layers of account protection</p>
                    </div>
                </div>
                <div class="pref-card-body">
                    <div class="pref-row">
                        <div class="pref-row-label">
                            <h6>Two-Factor Authentication</h6>
                            <p>Add an extra layer of security to your login</p>
                        </div>
                        <label class="rc-switch">
                            <input type="checkbox" id="enable2fa">
                            <span class="rc-track"><span class="rc-thumb"></span></span>
                        </label>
                    </div>

                    <div class="mt-3 pt-2">
                        <h6 style="font-size:0.85rem;font-weight:700;color:var(--text);margin-bottom:0.75rem;">Active Sessions</h6>
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:0.9rem;background:var(--bg-card2);border:1px solid var(--border);border-radius:12px;">
                            <div>
                                <div style="font-size:0.875rem;font-weight:600;color:var(--text);">Current Session</div>
                                <div style="font-size:0.78rem;color:var(--text-muted);">{{ request()->ip() }} &nbsp;·&nbsp; {{ now()->format('M j, Y') }}</div>
                            </div>
                            <span class="session-badge"><i class="bi bi-circle-fill" style="font-size:0.45rem;"></i> Active</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ══ NOTIFICATIONS ══ --}}
        <div class="settings-section" id="section-notifications">

            <div class="pref-card fade-in-card">
                <div class="pref-card-header">
                    <div class="pref-card-header-icon" style="background:rgba(16,185,129,0.12);color:var(--success);">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div>
                        <h6>Email Notifications</h6>
                        <p>Control what gets sent to your inbox</p>
                    </div>
                </div>
                <div class="pref-card-body">
                    @php
                        $emailToggles = [
                            ['id' => 'email_patients',  'title' => 'New Patient Registration',  'desc' => 'Get notified when new patients register', 'checked' => true],
                            ['id' => 'email_checkups',  'title' => 'Checkup Reminders',          'desc' => 'Remind me about scheduled checkups',        'checked' => true],
                            ['id' => 'email_forum',     'title' => 'Forum Activity',             'desc' => 'Updates on forum posts and comments',       'checked' => false],
                        ];
                    @endphp
                    @foreach($emailToggles as $t)
                        <div class="pref-row">
                            <div class="pref-row-label">
                                <h6>{{ $t['title'] }}</h6>
                                <p>{{ $t['desc'] }}</p>
                            </div>
                            <label class="rc-switch">
                                <input type="checkbox" id="{{ $t['id'] }}" {{ $t['checked'] ? 'checked' : '' }}>
                                <span class="rc-track"><span class="rc-thumb"></span></span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pref-card fade-in-card">
                <div class="pref-card-header">
                    <div class="pref-card-header-icon" style="background:rgba(6,182,212,0.12);color:var(--info);">
                        <i class="bi bi-app"></i>
                    </div>
                    <div>
                        <h6>In-App Notifications</h6>
                        <p>Control alerts within the application</p>
                    </div>
                </div>
                <div class="pref-card-body">
                    @php
                        $inAppToggles = [
                            ['id' => 'desktop_notifs', 'title' => 'Desktop Notifications', 'desc' => 'Show system notifications on your desktop', 'checked' => true],
                            ['id' => 'sound_alerts',   'title' => 'Sound Alerts',          'desc' => 'Play a sound for new notifications',         'checked' => false],
                        ];
                    @endphp
                    @foreach($inAppToggles as $t)
                        <div class="pref-row">
                            <div class="pref-row-label">
                                <h6>{{ $t['title'] }}</h6>
                                <p>{{ $t['desc'] }}</p>
                            </div>
                            <label class="rc-switch">
                                <input type="checkbox" id="{{ $t['id'] }}" {{ $t['checked'] ? 'checked' : '' }}>
                                <span class="rc-track"><span class="rc-thumb"></span></span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ══ PRIVACY ══ --}}
        <div class="settings-section" id="section-privacy">

            <div class="pref-card fade-in-card">
                <div class="pref-card-header">
                    <div class="pref-card-header-icon" style="background:rgba(155,54,255,0.12);color:var(--accent-violet);">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                    <div>
                        <h6>Profile Visibility</h6>
                        <p>Control who can see your profile</p>
                    </div>
                </div>
                <div class="pref-card-body">
                    @php
                        $privToggles = [
                            ['id' => 'public_profile', 'title' => 'Public Profile',          'desc' => 'Make your profile visible to patients',        'checked' => true],
                            ['id' => 'show_contact',   'title' => 'Show Contact Information', 'desc' => 'Display your email and phone to patients',     'checked' => false],
                        ];
                    @endphp
                    @foreach($privToggles as $t)
                        <div class="pref-row">
                            <div class="pref-row-label">
                                <h6>{{ $t['title'] }}</h6>
                                <p>{{ $t['desc'] }}</p>
                            </div>
                            <label class="rc-switch">
                                <input type="checkbox" id="{{ $t['id'] }}" {{ $t['checked'] ? 'checked' : '' }}>
                                <span class="rc-track"><span class="rc-thumb"></span></span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pref-card fade-in-card">
                <div class="pref-card-header">
                    <div class="pref-card-header-icon" style="background:rgba(239,68,68,0.12);color:var(--danger);">
                        <i class="bi bi-database-fill"></i>
                    </div>
                    <div>
                        <h6>Data Management</h6>
                        <p>Manage or delete your account data</p>
                    </div>
                </div>
                <div class="pref-card-body">
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-download me-1"></i> Download My Data
                        </button>
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-archive me-1"></i> Request Archive
                        </button>
                    </div>
                    <div class="danger-zone">
                        <h6><i class="bi bi-exclamation-triangle-fill"></i> Danger Zone</h6>
                        <p style="font-size:0.82rem;color:var(--text-muted);margin-bottom:0.85rem;">
                            Deleting your account is permanent and cannot be undone.
                        </p>
                        <button class="btn btn-danger btn-sm">
                            <i class="bi bi-trash-fill me-1"></i> Delete Account
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </div>{{-- /.settings-content --}}
</div>{{-- /.settings-wrap --}}

@push('scripts')
<script>
function showSection(id, el, ev) {
    if (ev) {
        ev.preventDefault();
    }

    // Hide all sections
    document.querySelectorAll('.settings-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.settings-nav-item').forEach(n => n.classList.remove('active'));
    // Show target
    document.getElementById('section-' + id).classList.add('active');
    el.classList.add('active');
    // Re-trigger fade-in for newly shown cards
    document.querySelectorAll('#section-' + id + ' .fade-in-card').forEach(c => {
        c.classList.remove('visible');
        setTimeout(() => c.classList.add('visible'), 30);
    });
}
// Init first section cards
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#section-appearance .fade-in-card').forEach((c, i) => {
        setTimeout(() => c.classList.add('visible'), i * 80);
    });
    // Sync theme option card active state
    const current = localStorage.getItem('rc_theme') || 'light';
    const optLight = document.getElementById('theme-opt-light');
    const optDark  = document.getElementById('theme-opt-dark');
    if (optLight) optLight.classList.toggle('active', current === 'light');
    if (optDark)  optDark.classList.toggle('active',  current === 'dark');

    document.querySelectorAll('.settings-nav-item').forEach(link => {
        link.addEventListener('click', function (ev) {
            ev.preventDefault();
        });
    });
});
</script>
@endpush

@endsection
