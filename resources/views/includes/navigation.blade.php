@php
    $currentUser = auth()->user();
    $currentRole = $currentUser?->role ?? null;
    $unreadNotifications = $currentUser?->notifications()->unread()->count() ?? 0;
    $dashboardRoute = match ($currentRole) {
        'cho' => route('cho.dashboard'),
        'rhu' => route('rhu.dashboard'),
        'user' => route('user.dashboard'),
        'midwife' => route('midwife.dashboard'),
        'bhw_president' => route('bhw-president.dashboard'),
        default => route('bhw.dashboard'),
    };
    $roleDisplay = match ($currentRole) {
        'cho' => 'CHO Administrator',
        'rhu' => 'RHU Administrator',
        'midwife' => 'RHU Midwife',
        'bhw_president' => 'BHW President',
        'bhw' => 'Barangay Health Worker',
        default => 'Patient / Client',
    };
    $roleBadgeBg = match ($currentRole) {
        'cho' => 'rgba(108, 92, 231, 0.12)',
        'rhu' => 'rgba(14, 165, 233, 0.12)',
        'midwife' => 'rgba(16, 185, 129, 0.12)',
        default => 'rgba(100, 116, 139, 0.12)',
    };
    $roleBadgeText = match ($currentRole) {
        'cho' => '#6C5CE7',
        'rhu' => '#0284C7',
        'midwife' => '#059669',
        default => '#475569',
    };
@endphp

@if($currentUser)
<nav class="navbar navbar-expand-lg border-bottom" style="background:#FFFFFF; border-color:#E2E8F0 !important; height:64px;">
    <div class="container-fluid px-3">
        {{-- Brand & Sidebar Toggle --}}
        <div class="d-flex align-items-center gap-2">
            <button id="sidebarToggleBtn"
                    class="btn btn-sm btn-light border d-flex align-items-center justify-content-center"
                    style="width:36px; height:36px; border-radius:10px; color:#64748B; background:#F8F9FA;"
                    aria-label="Toggle sidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <a class="navbar-brand d-flex align-items-center gap-2 m-0 p-0 text-decoration-none" href="{{ $dashboardRoute }}">
                <div class="d-flex align-items-center justify-content-center rounded-3 text-white" 
                     style="width:34px; height:34px; background:linear-gradient(135deg, #6C5CE7, #5E35B1);">
                    <i class="bi bi-heart-pulse-fill" style="font-size:1.1rem;"></i>
                </div>
                <span class="fw-800" style="font-family:'Plus Jakarta Sans',sans-serif; color:#1E293B; font-size:1.15rem; letter-spacing:-0.4px;">
                    Repro<span style="color:#6C5CE7;">Care</span>
                </span>
            </a>
        </div>

        {{-- Facility Dropdown & Global Search (Desktop) --}}
        <div class="d-none d-md-flex align-items-center gap-3 ms-4 flex-grow-1" style="max-width:550px;">
            {{-- Facility Selector --}}
            <div class="dropdown">
                <button class="btn btn-sm btn-light border d-flex align-items-center gap-2" 
                        type="button" data-bs-toggle="dropdown" 
                        style="border-radius:10px; background:#F8F9FA; color:#334155; font-size:0.82rem; font-weight:600; padding:0.4rem 0.75rem;">
                    <i class="bi bi-hospital text-primary"></i>
                    <span>{{ $currentRole === 'cho' ? 'City Health Office (CHO)' : ($currentRole === 'midwife' ? 'RHU Main Health Center' : 'Maternal Health System') }}</span>
                    <i class="bi bi-chevron-down text-muted" style="font-size:0.75rem;"></i>
                </button>
                <ul class="dropdown-menu shadow-sm border mt-1" style="border-radius:12px; font-size:0.85rem;">
                    <li><h6 class="dropdown-header text-xs text-uppercase fw-700">Health Units & Facilities</h6></li>
                    <li><a class="dropdown-item active d-flex align-items-center gap-2" href="#"><i class="bi bi-building"></i> City Health Office (CHO) - Central</a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#"><i class="bi bi-hospital"></i> RHU Rural Health Unit - Main</a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#"><i class="bi bi-geo-alt"></i> Barangay Health Stations (All)</a></li>
                </ul>
            </div>

            {{-- Global Search Bar --}}
            <form action="{{ $currentRole === 'midwife' ? route('midwife.patients') : ($currentRole === 'cho' ? route('cho.patients.index') : '#') }}" 
                  method="GET" class="w-100 position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size:0.85rem;"></i>
                <input type="search" name="search" class="form-control form-control-sm ps-5 bg-light border-0" 
                       placeholder="Search patient name, ID, or case..." 
                       style="border-radius:10px; font-size:0.84rem; height:36px; color:#1E293B;">
            </form>
        </div>

        {{-- Right Side Actions --}}
        <div class="d-flex align-items-center gap-2 ms-auto">
            {{-- Active Role Indicator Badge --}}
            <div class="d-none d-sm-inline-flex align-items-center gap-1 px-2.5 py-1 rounded-pill" 
                 style="background:{{ $roleBadgeBg }}; color:{{ $roleBadgeText }}; font-size:0.75rem; font-weight:700;">
                <span class="rounded-circle" style="width:6px; height:6px; background:currentColor;"></span>
                {{ $roleDisplay }}
            </div>

            {{-- System Alerts / Notification Bell --}}
            @php
                $notifRoute = match($currentRole) {
                    'cho' => route('cho.dashboard'),
                    'rhu' => route('rhu.dashboard'),
                    'midwife' => route('midwife.notifications.index'),
                    'bhw_president' => route('bhw-president.dashboard'),
                    'bhw' => route('bhw.notifications.index'),
                    default => route('user.notifications'),
                };
            @endphp
            <a href="{{ $notifRoute }}" class="btn btn-sm btn-light border position-relative d-flex align-items-center justify-content-center"
               style="width:36px; height:36px; border-radius:10px; color:#64748B; background:#F8F9FA;"
               title="System Alerts & Notifications">
                <i class="bi bi-bell fs-6"></i>
                @if($unreadNotifications > 0)
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                        <span class="visually-hidden">New alerts</span>
                    </span>
                @endif
            </a>

            {{-- User Avatar Dropdown --}}
            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 p-1 text-decoration-none"
                   href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ $currentUser->profile_image_url }}"
                         alt="{{ $currentUser->name }}"
                         class="rounded-circle border"
                         style="width:34px; height:34px; object-fit:cover; border-color:#E2E8F0;"
                         onerror="this.onerror=null;this.src='{{ $currentUser->gender === 'male' ? '/images/avatars/avatar-male.svg' : '/images/avatars/avatar-female.svg' }}';">
                    <div class="d-none d-lg-block text-start lh-1">
                        <div class="fw-700 text-truncate" style="max-width:110px; font-size:0.84rem; color:#1E293B;">
                            {{ $currentUser->first_name }}
                        </div>
                        <small class="text-muted" style="font-size:0.7rem;">{{ ucfirst($currentRole) }}</small>
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2" style="border-radius:14px; min-width:210px; font-size:0.85rem;">
                    <li class="px-3 py-2 border-bottom">
                        <div class="fw-700 text-dark">{{ $currentUser->name }}</div>
                        <div class="text-muted text-xs">{{ $currentUser->email }}</div>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('profile.show') }}">
                            <i class="bi bi-person-circle text-primary"></i> My Profile
                        </a>
                    </li>
                    @if($currentRole === 'cho')
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('cho.settings') }}">
                                <i class="bi bi-gear text-primary"></i> System Settings
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('cho.archived.index') }}">
                                <i class="bi bi-archive text-secondary"></i> Archived Records
                            </a>
                        </li>
                    @elseif($currentRole === 'midwife')
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('midwife.learning.index') }}">
                                <i class="bi bi-camera-video text-danger"></i> Media & Training
                            </a>
                        </li>
                    @endif
                    <li class="border-top">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger py-2 d-flex align-items-center gap-2">
                                <i class="bi bi-box-arrow-right"></i> Sign Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
@endif
