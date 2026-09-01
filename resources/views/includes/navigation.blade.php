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
    $roleChipClass = match ($currentRole) {
        'cho' => 'cho',
        'rhu' => 'rhu',
        'midwife' => 'midwife',
        'bhw' => 'bhw',
        'bhw_president' => 'bhw-president',
        default => 'user',
    };
@endphp

@if($currentUser)
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ $dashboardRoute }}">
            <div class="brand-icon">
                <img src="{{ asset('images/brand/reprocare-logo.svg') }}" alt="ReproCare logo" class="brand-logo-image">
            </div>
            <span>ReproCare</span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <button id="sidebarToggleBtn"
                    class="btn btn-sm"
                    style="background: var(--primary-subtle); border: 1px solid var(--border); color: var(--primary-light); border-radius: 10px; padding: 0.35rem 0.6rem;"
                    aria-label="Toggle sidebar">
                <i class="bi bi-layout-sidebar" style="font-size: 1.15rem;"></i>
            </button>
            <button class="navbar-toggler border-0 d-lg-none"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNav"
                    aria-controls="navbarNav"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                <i class="bi bi-three-dots-vertical" style="color: var(--text-muted); font-size: 1.2rem;"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                <li class="nav-item me-1">
                    <span class="role-chip role-chip-{{ $roleChipClass }}">
                        @if($currentRole === 'cho')
                            <i class="bi bi-building-fill me-1"></i>CHO Admin
                        @elseif($currentRole === 'rhu')
                            <i class="bi bi-hospital-fill me-1"></i>RHU Admin
                        @elseif($currentRole === 'midwife')
                            <i class="bi bi-clipboard2-heart-fill me-1"></i>Midwife
                        @elseif($currentRole === 'bhw')
                            <i class="bi bi-person-workspace me-1"></i>BHW
                        @elseif($currentRole === 'bhw_president')
                            <i class="bi bi-person-badge-fill me-1"></i>BHW President
                        @else
                            <i class="bi bi-person-heart me-1"></i>Woman
                        @endif
                    </span>
                </li>

                <li class="nav-item">
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
                    <a class="nav-link notif-bell-wrap" href="{{ $notifRoute }}" aria-label="Notifications">
                        <i class="bi bi-bell" style="font-size: 1.1rem;"></i>
                        @if($unreadNotifications > 0)
                            <span class="notif-dot"></span>
                        @endif
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 px-2"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        <img src="{{ $currentUser->profile_image_url }}"
                             alt="{{ $currentUser->name }}"
                             class="nav-avatar"
                             onerror="this.onerror=null;this.src='{{ $currentUser->gender === 'male'
                                 ? '/images/avatars/avatar-male.svg'
                                 : '/images/avatars/avatar-female.svg' }}';">
                        <span style="max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.875rem; font-weight: 600; color: var(--text);">
                            {{ $currentUser->name }}
                        </span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end mt-2">
                        <li>
                            <div class="px-3 py-2 mb-1" style="border-bottom: 1px solid var(--border);">
                                <div style="font-size: 0.82rem; font-weight: 700; color: var(--text);">
                                    {{ $currentUser->name }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">
                                    {{ $currentUser->email }}
                                </div>
                            </div>
                        </li>

                        <li>
                            <a class="dropdown-item" href="{{ route('profile.show') }}">
                                <i class="bi bi-person-circle me-2" style="color: var(--primary-light);"></i>
                                {{ $currentRole === 'user' ? 'Woman Profile' : 'My Profile' }}
                            </a>
                        </li>

                        @if($currentRole === 'cho')
                            <li>
                                <a class="dropdown-item" href="{{ route('cho.settings') }}">
                                    <i class="bi bi-gear me-2" style="color: var(--primary-light);"></i>
                                    Settings
                                </a>
                            </li>
                        @elseif($currentRole === 'rhu')
                            <li>
                                <a class="dropdown-item" href="{{ route('rhu.settings') }}">
                                    <i class="bi bi-gear me-2" style="color: var(--primary-light);"></i>
                                    Settings
                                </a>
                            </li>
                        @elseif($currentRole === 'midwife')
                            <li>
                                <a class="dropdown-item" href="{{ route('midwife.settings') }}">
                                    <i class="bi bi-gear me-2" style="color: var(--primary-light);"></i>
                                    Settings
                                </a>
                            </li>
                        @elseif($currentRole === 'bhw')
                            <li>
                                <a class="dropdown-item" href="{{ route('bhw.settings') }}">
                                    <i class="bi bi-gear me-2" style="color: var(--primary-light);"></i>
                                    Settings
                                </a>
                            </li>
                        @else
                            <li>
                                <a class="dropdown-item" href="{{ route('user.settings') }}">
                                    <i class="bi bi-gear me-2" style="color: var(--primary-light);"></i>
                                    Settings
                                </a>
                            </li>
                        @endif

                        <li>
                            <div class="dropdown-item d-flex align-items-center justify-content-between"
                                 style="cursor: default;">
                                <span style="font-size: 0.875rem;">
                                    <i class="bi bi-circle-half me-2" style="color: var(--primary-light);"></i>
                                    Dark Mode
                                </span>
                                <label class="rc-switch mb-0">
                                    <input type="checkbox"
                                           class="theme-switch-input"
                                           onclick="event.stopPropagation();">
                                    <span class="rc-track">
                                        <span class="rc-thumb"></span>
                                    </span>
                                </label>
                            </div>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    .navbar .dropdown-item:has(.rc-switch) { cursor: default; user-select: none; }
    .navbar .dropdown-item:has(.rc-switch):hover { background: transparent; }
    
    .role-chip-cho {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        box-shadow: 0 2px 10px rgba(16,185,129,0.35);
    }
    .role-chip-rhu {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #fff;
        box-shadow: 0 2px 10px rgba(99,102,241,0.35);
    }
</style>
@endif
