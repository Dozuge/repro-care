@extends('bhw.layout')

@section('title', 'Settings - BHW Portal | ReproCare')

@push('styles')
<style>
    .settings-wrap         { display: flex; gap: 1.5rem; align-items: flex-start; }
    .settings-sidebar      { width: 240px; flex-shrink: 0; position: sticky; top: 80px; }
    .settings-content      { flex: 1; min-width: 0; }
    .settings-nav          { background: var(--bg-card); border: 1px solid var(--border); border-radius: 18px; overflow: hidden; padding: 0.5rem; }
    .settings-nav-item     { display: flex; align-items: center; gap: 0.75rem; padding: 0.7rem 0.9rem; border-radius: 12px; font-size: 0.875rem; font-weight: 500; color: var(--text-muted); cursor: pointer; text-decoration: none; transition: all 0.2s ease; border: 1px solid transparent; margin-bottom: 0.15rem; }
    .settings-nav-item i   { font-size: 1rem; width: 1.15rem; text-align: center; flex-shrink: 0; }
    .settings-nav-item:hover { background: var(--primary-subtle); color: var(--text); }
    .settings-nav-item.active { background: linear-gradient(135deg, var(--primary-subtle), rgba(155,54,255,0.10)); color: var(--primary-light); border-color: var(--border-glass); font-weight: 600; }
    .settings-section      { display: none; }
    .settings-section.active { display: block; }
    .pref-card             { background: var(--bg-card); border: 1px solid var(--border); border-radius: 18px; margin-bottom: 1.25rem; overflow: hidden; transition: background 0.4s ease; }
    .pref-card-header      { padding: 1.1rem 1.4rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 0.75rem; }
    .pref-card-header-icon { width: 38px; height: 38px; border-radius: 11px; background: var(--primary-subtle); border: 1px solid var(--border-glass); display: flex; align-items: center; justify-content: center; font-size: 1rem; color: var(--primary-light); flex-shrink: 0; }
    .pref-card-header h6   { margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; font-size: 0.95rem; color: var(--text); }
    .pref-card-header p    { margin: 0; font-size: 0.78rem; color: var(--text-muted); }
    .pref-card-body        { padding: 1.25rem 1.4rem; }
    .pref-row { display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 0; border-bottom: 1px solid var(--border); }
    .pref-row:last-child { border-bottom: none; padding-bottom: 0; }
    .pref-row-label h6 { margin: 0 0 0.15rem; font-size: 0.9rem; font-weight: 600; color: var(--text); }
    .pref-row-label p  { margin: 0; font-size: 0.8rem; color: var(--text-muted); }
    .theme-option-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem; }
    .theme-option { border: 2px solid var(--border); border-radius: 14px; padding: 1rem; cursor: pointer; text-align: center; transition: all 0.25s ease; position: relative; background: var(--bg-card2); }
    .theme-option:hover { border-color: var(--primary); transform: translateY(-2px); }
    .theme-option.active { border-color: var(--primary); background: var(--primary-subtle); }
    .theme-preview { border-radius: 10px; overflow: hidden; height: 70px; display: flex; margin-bottom: 0.75rem; border: 1px solid var(--border); }
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
    .theme-toggle-row { display: flex; align-items: center; gap: 0.9rem; margin-top: 1.25rem; padding: 0.85rem 1rem; background: var(--bg-card2); border: 1px solid var(--border); border-radius: 12px; }
    .theme-toggle-row span { font-size: 0.875rem; color: var(--text-muted); font-weight: 500; display: flex; align-items: center; gap: 0.4rem; }
    .danger-zone { border: 1px solid rgba(239,68,68,0.25); border-radius: 16px; padding: 1.25rem 1.4rem; background: rgba(239,68,68,0.05); }
    .danger-zone h6 { color: #f87171; font-weight: 700; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem; }
    .session-badge { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.25rem 0.7rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); border-radius: 20px; font-size: 0.78rem; font-weight: 600; color: #34d399; }
    .settings-page-header { margin-bottom: 1.75rem; }
    .settings-page-header h1 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.5rem; font-weight: 800; color: var(--text); margin-bottom: 0.2rem; }
    .settings-page-header p  { font-size: 0.875rem; color: var(--text-muted); margin: 0; }
    @media (max-width: 768px) {
        .settings-wrap { flex-direction: column; }
        .settings-sidebar { width: 100%; position: static; }
        .theme-option-grid { grid-template-columns: 1fr 1fr; }
    }
</style>
@endpush

@section('bhw-content')

<div class="settings-page-header fade-in-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-gear-fill me-2" style="color:var(--info);"></i>Settings</h1>
            <p>Manage your account preferences and system settings</p>
        </div>
        <a href="{{ route('profile.show') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-person-circle me-1"></i> Back to Profile
        </a>
    </div>
</div>

<div class="settings-wrap">

    {{-- LEFT NAV --}}
    <div class="settings-sidebar fade-in-card">
        <div class="settings-nav">
            <a class="settings-nav-item active" onclick="showSection('appearance', this)" href="#">
                <i class="bi bi-palette-fill" style="color:var(--primary-light);"></i> Appearance
            </a>
            <a class="settings-nav-item" onclick="showSection('account', this)" href="#">
                <i class="bi bi-person-fill" style="color:var(--info);"></i> Account
            </a>
            <a class="settings-nav-item" onclick="showSection('security', this)" href="#">
                <i class="bi bi-shield-lock-fill" style="color:var(--warning);"></i> Security
            </a>
            <a class="settings-nav-item" onclick="showSection('notifications', this)" href="#">
                <i class="bi bi-bell-fill" style="color:var(--success);"></i> Notifications
            </a>
            <a class="settings-nav-item" onclick="showSection('privacy', this)" href="#">
                <i class="bi bi-lock-fill" style="color:var(--danger);"></i> Privacy
            </a>
        </div>
    </div>

    <div class="settings-content">

        {{-- APPEARANCE --}}
        <div class="settings-section active" id="section-appearance">
            <div class="pref-card fade-in-card">
                <div class="pref-card-header">
                    <div class="pref-card-header-icon"><i class="bi bi-palette-fill"></i></div>
                    <div><h6>Color Theme</h6><p>Choose your preferred interface appearance</p></div>
                </div>
                <div class="pref-card-body">
                    <div class="theme-option-grid">
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

        {{-- ACCOUNT --}}
        <div class="settings-section" id="section-account">
            <div class="pref-card fade-in-card">
                <div class="pref-card-header">
                    <div class="pref-card-header-icon" style="background:rgba(6,182,212,0.12);color:var(--info);"><i class="bi bi-person-fill"></i></div>
                    <div><h6>Account Information</h6><p>Update your personal details</p></div>
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
                                            <i class="bi bi-geo-alt-fill me-2" style="color:var(--info);"></i>Coverage Location
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
                                                <iframe
                                                    title="Barangay Burgos Padlan Map"
                                                    src="https://www.openstreetmap.org/export/embed.html?bbox=120.3378%2C15.9181%2C120.3581%2C15.9381&layer=mapnik&marker=15.92806%2C120.3478"
                                                    width="100%"
                                                    height="280"
                                                    style="border:0; border-radius:12px;"
                                                    loading="lazy"
                                                    referrerpolicy="strict-origin-when-cross-origin"></iframe>
                                                <div class="mt-2" style="font-size:0.8rem; color:var(--text-muted);">
                                                    If the map does not load, <a href="https://www.google.com/maps/search/?api=1&query=Barangay+Burgos+Padlan+San+Carlos+City+Pangasinan" target="_blank" rel="noopener noreferrer">open the location in Google Maps</a>.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 d-flex gap-2 pt-1">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> Save Changes</button>
                                <button type="button" class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i> Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- SECURITY --}}
        <div class="settings-section" id="section-security">
            <div class="pref-card fade-in-card">
                <div class="pref-card-header">
                    <div class="pref-card-header-icon" style="background:rgba(245,158,11,0.12);color:var(--warning);"><i class="bi bi-key-fill"></i></div>
                    <div><h6>Change Password</h6><p>Update your login password</p></div>
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
                            <button type="submit" class="btn btn-primary"><i class="bi bi-shield-lock me-1"></i> Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="pref-card fade-in-card">
                <div class="pref-card-header">
                    <div class="pref-card-header-icon" style="background:rgba(16,185,129,0.12);color:var(--success);"><i class="bi bi-shield-fill-check"></i></div>
                    <div><h6>Security Options</h6><p>Extra layers of account protection</p></div>
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

        {{-- NOTIFICATIONS --}}
        <div class="settings-section" id="section-notifications">
            <div class="pref-card fade-in-card">
                <div class="pref-card-header">
                    <div class="pref-card-header-icon" style="background:rgba(16,185,129,0.12);color:var(--success);"><i class="bi bi-envelope-fill"></i></div>
                    <div><h6>Email Notifications</h6><p>Control what gets sent to your inbox</p></div>
                </div>
                <div class="pref-card-body">
                    @foreach([
                        ['id'=>'email_checkups',  'title'=>'Checkup Reminders',        'desc'=>'Remind me about upcoming scheduled checkups', 'on'=>true],
                        ['id'=>'email_schedule',  'title'=>'Schedule Updates',          'desc'=>'Notify when patient schedules change',         'on'=>true],
                        ['id'=>'email_forum',     'title'=>'Forum Activity',            'desc'=>'Updates on forum posts and comments',           'on'=>false],
                    ] as $t)
                    <div class="pref-row">
                        <div class="pref-row-label"><h6>{{ $t['title'] }}</h6><p>{{ $t['desc'] }}</p></div>
                        <label class="rc-switch">
                            <input type="checkbox" id="{{ $t['id'] }}" {{ $t['on'] ? 'checked' : '' }}>
                            <span class="rc-track"><span class="rc-thumb"></span></span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="pref-card fade-in-card">
                <div class="pref-card-header">
                    <div class="pref-card-header-icon" style="background:rgba(6,182,212,0.12);color:var(--info);"><i class="bi bi-app"></i></div>
                    <div><h6>In-App Notifications</h6><p>Control alerts within the application</p></div>
                </div>
                <div class="pref-card-body">
                    @foreach([
                        ['id'=>'desktop_notifs','title'=>'Desktop Notifications','desc'=>'Show system notifications on desktop','on'=>true],
                        ['id'=>'sound_alerts',  'title'=>'Sound Alerts',          'desc'=>'Play a sound for new notifications', 'on'=>false],
                    ] as $t)
                    <div class="pref-row">
                        <div class="pref-row-label"><h6>{{ $t['title'] }}</h6><p>{{ $t['desc'] }}</p></div>
                        <label class="rc-switch">
                            <input type="checkbox" id="{{ $t['id'] }}" {{ $t['on'] ? 'checked' : '' }}>
                            <span class="rc-track"><span class="rc-thumb"></span></span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- PRIVACY --}}
        <div class="settings-section" id="section-privacy">
            <div class="pref-card fade-in-card">
                <div class="pref-card-header">
                    <div class="pref-card-header-icon" style="background:rgba(239,68,68,0.12);color:var(--danger);"><i class="bi bi-database-fill"></i></div>
                    <div><h6>Data Management</h6><p>Manage or delete your account data</p></div>
                </div>
                <div class="pref-card-body">
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <button class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-1"></i> Download My Data</button>
                        <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-archive me-1"></i> Request Archive</button>
                    </div>
                    <div class="danger-zone">
                        <h6><i class="bi bi-exclamation-triangle-fill"></i> Danger Zone</h6>
                        <p style="font-size:0.82rem;color:var(--text-muted);margin-bottom:0.85rem;">Deleting your account is permanent and cannot be undone.</p>
                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash-fill me-1"></i> Delete Account</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function showSection(id, el) {
    event.preventDefault();
    document.querySelectorAll('.settings-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.settings-nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    el.classList.add('active');
    document.querySelectorAll('#section-' + id + ' .fade-in-card').forEach((c,i) => {
        c.classList.remove('visible');
        setTimeout(() => c.classList.add('visible'), i * 60 + 30);
    });
}
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#section-appearance .fade-in-card').forEach((c,i) => {
        setTimeout(() => c.classList.add('visible'), i * 80);
    });
    const current = localStorage.getItem('rc_theme') || 'light';
    const ol = document.getElementById('theme-opt-light');
    const od = document.getElementById('theme-opt-dark');
    if (ol) ol.classList.toggle('active', current === 'light');
    if (od) od.classList.toggle('active', current === 'dark');
});
</script>
@endpush

@endsection
