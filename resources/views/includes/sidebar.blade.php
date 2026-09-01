@php
    $user = auth()->user();
    $role = null;
    $roleLabel = null;
    $roleColor = null;
    $unreadMsgs = 0;
    $pendingCount = 0;
    $routeRole = null;

    if ($user) {
        $role = $user->role;
        $routeRole = $role === 'bhw_president' ? 'bhw-president' : $role;
        if ($role === 'cho') {
            $roleLabel = 'CHO Admin';
            $roleColor = '#10b981';
        } elseif ($role === 'rhu') {
            // Display as RHU1 (Rural Health Unit 1) per new role naming
            $roleLabel = 'RHU1 (Rural Health Unit 1)';
            $roleColor = '#6366f1';
        } elseif ($role === 'midwife') {
            $roleLabel = 'Midwife';
            // Ensure readable contrast (avoid using very light CSS variables here)
            $roleColor = '#7c3aed';
            $unreadMsgs = \App\Models\Message::where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();
            $pendingCount = \App\Models\User::where('role', 'user')->where('status', 'pending')->count();
        } elseif ($role === 'bhw') {
            $roleLabel = 'BHW';
            $roleColor = '#22d3ee';
            $unreadMsgs = \App\Models\Message::where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();
            $pendingCount = \App\Models\User::where('role', 'user')->where('status', 'pending')->count();
        } elseif ($role === 'bhw_president') {
            $roleLabel = 'BHW President';
            $roleColor = '#8b5cf6';
            $unreadMsgs = \App\Models\Message::where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();
            $pendingCount = \App\Models\User::where('role', 'user')->where('status', 'pending')->count();
        } elseif ($role === 'user') {
            $roleLabel = 'Patient';
            // readable patient label color
            $roleColor = '#6366f1';
            $unreadMsgs = \App\Models\Message::where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();
        }
    }
@endphp

@if($user)
<nav class="sidebar" id="sidebar">
    <div class="sidebar-portal-label">
        @if($role === 'cho')
            <i class="bi bi-building-fill me-1"></i> CHO Portal
        @elseif($role === 'rhu')
            <i class="bi bi-hospital-fill me-1"></i> RHU Portal
        @elseif($role === 'user')
            <i class="bi bi-person-heart me-1"></i> Patient Portal
        @elseif($role === 'midwife')
            <i class="bi bi-clipboard2-heart-fill me-1"></i> Midwife Portal
        @elseif($role === 'bhw_president')
            <i class="bi bi-person-badge-fill me-1"></i> BHW President Portal
        @else
            <i class="bi bi-person-workspace me-1"></i> BHW Portal
        @endif
    </div>

    @if($role === 'cho')
        <div class="sidebar-section-label">Overview</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('cho.dashboard') ? 'active' : '' }}"
                   href="{{ route('cho.dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('cho.analytics') ? 'active' : '' }}"
                   href="{{ route('cho.analytics') }}">
                    <i class="bi bi-bar-chart-fill"></i>
                    <span>Analytics</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Management</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('cho.users*') ? 'active' : '' }}"
                   href="{{ route('cho.users.index') }}">
                    <i class="bi bi-people-fill"></i>
                    <span>User Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('cho.supply-requests*') ? 'active' : '' }}"
                   href="{{ route('cho.supply-requests.index') }}">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>Supply Requests</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Maternal Audit</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('cho.maternal-deaths*') ? 'active' : '' }}"
                   href="{{ route('cho.maternal-deaths.index') }}">
                    <i class="bi bi-journal-x"></i>
                    <span>Maternal Deaths</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">System</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('cho.logs*') ? 'active' : '' }}"
                   href="{{ route('cho.logs.index') }}">
                    <i class="bi bi-activity"></i>
                    <span>Activity Logs</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('cho.settings') ? 'active' : '' }}"
                   href="{{ route('cho.settings') }}">
                    <i class="bi bi-gear-fill"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>

    @elseif($role === 'rhu')
        <div class="sidebar-section-label">Overview</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('rhu.dashboard') ? 'active' : '' }}"
                   href="{{ route('rhu.dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('rhu.reports*') ? 'active' : '' }}"
                   href="{{ route('rhu.reports.index') }}">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                    <span>FHSIS Reports</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Management</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('rhu.midwives*') ? 'active' : '' }}"
                   href="{{ route('rhu.midwives.index') }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Midwife Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('rhu.bhw-presidents*') ? 'active' : '' }}"
                   href="{{ route('rhu.bhw-presidents.index') }}">
                    <i class="bi bi-person-badge-fill"></i>
                    <span>BHW Presidents</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('rhu.supply-requests*') ? 'active' : '' }}"
                   href="{{ route('rhu.supply-requests.index') }}">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>Supply Requests</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Maternal Health</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('rhu.maternal-deaths*') ? 'active' : '' }}"
                   href="{{ route('rhu.maternal-deaths.index') }}">
                    <i class="bi bi-journal-x"></i>
                    <span>Maternal Deaths</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('rhu.morbidities*') ? 'active' : '' }}"
                   href="{{ route('rhu.morbidities.index') }}">
                    <i class="bi bi-heart-pulse-fill"></i>
                    <span>Near-Miss Events</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">BHW Submissions</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('rhu.bhw-reports*') ? 'active' : '' }}"
                   href="{{ route('rhu.bhw-reports.index') }}">
                    <i class="bi bi-file-earmark-text-fill"></i>
                    <span>BHW Monthly Reports</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">System</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('rhu.database*') ? 'active' : '' }}"
                   href="{{ route('rhu.database.index') }}">
                    <i class="bi bi-database-fill-gear"></i>
                    <span>Database Backup</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('rhu.logs*') ? 'active' : '' }}"
                   href="{{ route('rhu.logs.index') }}">
                    <i class="bi bi-activity"></i>
                    <span>Activity Logs</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('rhu.settings') ? 'active' : '' }}"
                   href="{{ route('rhu.settings') }}">
                    <i class="bi bi-gear-fill"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>

    @elseif($role === 'user')
        <div class="sidebar-section-label">My Health</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('user.pregnancies*') ? 'active' : '' }}"
                   href="{{ route('user.pregnancies.index') }}">
                    <i class="bi bi-heart-pulse-fill"></i>
                    <span>Pregnancy Tracking</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('user.menstruation*') ? 'active' : '' }}"
                   href="#menstruationMenu"
                   data-bs-toggle="collapse"
                   role="button"
                   aria-expanded="{{ request()->routeIs('user.menstruation*') ? 'true' : 'false' }}"
                   aria-controls="menstruationMenu">
                    <i class="bi bi-calendar-heart-fill"></i>
                    <span>Menstrual Cycle</span>
                    <i class="bi bi-chevron-down ms-auto"
                       style="font-size: 0.7rem; transition: transform 0.25s ease;"
                       id="menstruChevron"></i>
                </a>
                <div class="collapse {{ request()->routeIs('user.menstruation*') ? 'show' : '' }}"
                     id="menstruationMenu">
                    <div class="sub-menu">
                        <a class="nav-link {{ request()->routeIs('user.menstruation.index') ? 'active' : '' }}"
                           href="{{ route('user.menstruation.index') }}">
                            <i class="bi bi-list-ul"></i>
                            <span>Overview</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('user.menstruation.calendar') ? 'active' : '' }}"
                           href="{{ route('user.menstruation.calendar') }}">
                            <i class="bi bi-calendar3"></i>
                            <span>Calendar</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('user.menstruation.statistics') ? 'active' : '' }}"
                           href="{{ route('user.menstruation.statistics') }}">
                            <i class="bi bi-bar-chart-line-fill"></i>
                            <span>Statistics</span>
                        </a>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('user.checkups*') ? 'active' : '' }}"
                   href="{{ route('user.checkups') }}">
                    <i class="bi bi-clipboard-heart-fill"></i>
                    <span>Checkups</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('user.health-records*') ? 'active' : '' }}"
                   href="{{ route('user.health-records') }}">
                    <i class="bi bi-file-medical-fill"></i>
                    <span>Health Records</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Community</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('user.messages.*') ? 'active' : '' }}"
                   href="{{ route('user.messages.index') }}">
                    <i class="bi bi-chat-text-fill"></i>
                    <span>Messages</span>
                    @if($unreadMsgs > 0)
                        <span style="background:rgba(139,92,246,0.2);color:var(--primary-light);font-size:0.68rem;font-weight:700;padding:0.1em 0.5em;border-radius:20px;margin-left:auto;">{{ $unreadMsgs }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('forum.*') ? 'active' : '' }}"
                   href="{{ route('forum.index') }}">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>Community Forum</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('learning.*') ? 'active' : '' }}"
                   href="{{ route('learning.index') }}">
                    <i class="bi bi-book-fill"></i>
                    <span>Learning Materials</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">System</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('user.notifications*') ? 'active' : '' }}"
                   href="{{ route('user.notifications') }}">
                    <i class="bi bi-bell-fill"></i>
                    <span>Notifications</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('user.settings') ? 'active' : '' }}"
                   href="{{ route('user.settings') }}">
                    <i class="bi bi-gear-fill"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    @elseif($role === 'midwife')
        <div class="sidebar-section-label">Overview</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.dashboard') ? 'active' : '' }}"
                   href="{{ route('midwife.dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Women &amp; Care</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.patients*') ? 'active' : '' }}"
                   href="{{ route('midwife.patients') }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Women</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.pending-patients') ? 'active' : '' }}"
                   href="{{ route('midwife.pending-patients') }}">
                    <i class="bi bi-person-check-fill"></i>
                    <span>Account Verification</span>
                    @if($pendingCount > 0)
                        <span style="background:rgba(245,158,11,0.2);color:#f59e0b;font-size:0.68rem;font-weight:700;padding:0.1em 0.5em;border-radius:20px;margin-left:auto;">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.pregnancies*') ? 'active' : '' }}"
                   href="{{ route('midwife.pregnancies.index') }}">
                    <i class="bi bi-heart-fill"></i>
                    <span>Pregnancies</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.checkups*') ? 'active' : '' }}"
                   href="{{ route('midwife.checkups.index') }}">
                    <i class="bi bi-clipboard-heart-fill"></i>
                    <span>Checkups</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.referrals*') ? 'active' : '' }}"
                   href="{{ route('midwife.referrals.index') }}">
                    <i class="bi bi-share-fill"></i>
                    <span>Checkup Referrals</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.health-records*') ? 'active' : '' }}"
                   href="{{ route('midwife.health-records.index') }}">
                    <i class="bi bi-file-medical-fill"></i>
                    <span>Health Records</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">BHW Management</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.bhw-presidents*') ? 'active' : '' }}"
                   href="{{ route('midwife.bhw-presidents.index') }}">
                    <i class="bi bi-person-badge-fill"></i>
                    <span>BHW President</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Content</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.sms.*') ? 'active' : '' }}"
                   href="{{ route('midwife.sms.index') }}">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>SMS Alerts</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.messages.*') ? 'active' : '' }}"
                   href="{{ route('midwife.messages.index') }}">
                    <i class="bi bi-chat-text-fill"></i>
                    <span>Messages</span>
                    @if($unreadMsgs > 0)
                        <span style="background:rgba(139,92,246,0.2);color:var(--primary-light);font-size:0.68rem;font-weight:700;padding:0.1em 0.5em;border-radius:20px;margin-left:auto;">{{ $unreadMsgs }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('forum.*') && !request()->routeIs('midwife.forum.admin.*') ? 'active' : '' }}"
                   href="{{ route('forum.index') }}">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>Community Forum</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.forum.admin.*') ? 'active' : '' }}"
                   href="{{ route('midwife.forum.admin.index') }}">
                    <i class="bi bi-shield-check"></i>
                    <span>Forum Admin</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.learning*') || request()->routeIs('learning.*') ? 'active' : '' }}"
                   href="{{ route('midwife.learning.index') }}">
                    <i class="bi bi-mortarboard-fill"></i>
                    <span>Learning Materials</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Reports</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.reports*') ? 'active' : '' }}"
                   href="{{ route('midwife.reports.index') }}">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.bhw-reports*') ? 'active' : '' }}"
                   href="{{ route('midwife.bhw-reports.index') }}">
                    <i class="bi bi-file-earmark-text-fill"></i>
                    <span>BHW Monthly Reports</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.maternal-care-target-clients*') ? 'active' : '' }}"
                   href="{{ route('midwife.maternal-care-target-clients.index') }}">
                    <i class="bi bi-table"></i>
                    <span>Maternal Client List</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.child-care-target-clients*') ? 'active' : '' }}"
                   href="{{ route('midwife.child-care-target-clients.index') }}">
                    <i class="bi bi-tablet-landscape"></i>
                    <span>Childcare Client List</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">System</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.notifications*') ? 'active' : '' }}"
                   href="{{ route('midwife.notifications.index') }}">
                    <i class="bi bi-bell-fill"></i>
                    <span>Notifications</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('midwife.settings') ? 'active' : '' }}"
                   href="{{ route('midwife.settings') }}">
                    <i class="bi bi-gear-fill"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    @elseif($role === 'bhw_president')
        <div class="sidebar-section-label">Overview</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw-president.dashboard') ? 'active' : '' }}"
                   href="{{ route('bhw-president.dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">BHW Management</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw-president.pending-patients') ? 'active' : '' }}"
                   href="{{ route('bhw-president.pending-patients') }}">
                    <i class="bi bi-person-check-fill"></i>
                    <span>Account Verification</span>
                    @if($pendingCount > 0)
                        <span style="background:rgba(245,158,11,0.2);color:#f59e0b;font-size:0.68rem;font-weight:700;padding:0.1em 0.5em;border-radius:20px;margin-left:auto;">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw-president.bhws*') ? 'active' : '' }}"
                   href="{{ route('bhw-president.bhws.index') }}">
                    <i class="bi bi-people-fill"></i>
                    <span>All BHWs</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Analytics</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw-president.analytics') ? 'active' : '' }}"
                   href="{{ route('bhw-president.analytics') }}">
                    <i class="bi bi-bar-chart-fill"></i>
                    <span>Health Analytics</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw-president.coverage') ? 'active' : '' }}"
                   href="{{ route('bhw-president.coverage') }}">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>Coverage Report</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw-president.high-risk') ? 'active' : '' }}"
                   href="{{ route('bhw-president.high-risk') }}">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>High-Risk Cases</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Reports</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw-president.reports*') ? 'active' : '' }}"
                   href="{{ route('bhw-president.reports.index') }}">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                    <span>Monthly Reports</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Content</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw-president.messages.*') ? 'active' : '' }}"
                   href="{{ route('bhw-president.messages.index') }}">
                    <i class="bi bi-chat-text-fill"></i>
                    <span>Messages</span>
                    @if($unreadMsgs > 0)
                        <span style="background:rgba(139,92,246,0.2);color:var(--primary-light);font-size:0.68rem;font-weight:700;padding:0.1em 0.5em;border-radius:20px;margin-left:auto;">{{ $unreadMsgs }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('forum.*') ? 'active' : '' }}"
                   href="{{ route('forum.index') }}">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>Community Forum</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('learning.*') ? 'active' : '' }}"
                   href="{{ route('learning.index') }}">
                    <i class="bi bi-mortarboard-fill"></i>
                    <span>Learning Materials</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">System</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw-president.settings') ? 'active' : '' }}"
                   href="{{ route('bhw-president.settings') }}">
                    <i class="bi bi-gear-fill"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    @else
        <div class="sidebar-section-label">Overview</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw.dashboard') ? 'active' : '' }}"
                   href="{{ route('bhw.dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Women &amp; Care</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw.patients*') ? 'active' : '' }}"
                   href="{{ route('bhw.patients') }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Women</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw.pending-patients') ? 'active' : '' }}"
                   href="{{ route('bhw.pending-patients') }}">
                    <i class="bi bi-person-check-fill"></i>
                    <span>Account Verification</span>
                    @if($pendingCount > 0)
                        <span style="background:rgba(245,158,11,0.2);color:#f59e0b;font-size:0.68rem;font-weight:700;padding:0.1em 0.5em;border-radius:20px;margin-left:auto;">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw.checkups*') ? 'active' : '' }}"
                   href="{{ route('bhw.checkups.index') }}">
                    <i class="bi bi-clipboard-heart-fill"></i>
                    <span>Checkups</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw.pregnancies*') ? 'active' : '' }}"
                   href="{{ route('bhw.pregnancies.index') }}">
                    <i class="bi bi-heart-fill"></i>
                    <span>Pregnancies</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw.health-records*') ? 'active' : '' }}"
                   href="{{ route('bhw.health-records.index') }}">
                    <i class="bi bi-file-medical-fill"></i>
                    <span>Health Records</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Content</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw.sms.*') ? 'active' : '' }}"
                   href="{{ route('bhw.sms.index') }}">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>SMS Alerts</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw.messages.*') ? 'active' : '' }}"
                   href="{{ route('bhw.messages.index') }}">
                    <i class="bi bi-chat-text-fill"></i>
                    <span>Messages</span>
                    @if($unreadMsgs > 0)
                        <span style="background:rgba(139,92,246,0.2);color:var(--primary-light);font-size:0.68rem;font-weight:700;padding:0.1em 0.5em;border-radius:20px;margin-left:auto;">{{ $unreadMsgs }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('forum.*') ? 'active' : '' }}"
                   href="{{ route('forum.index') }}">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>Community Forum</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw.learning*') || request()->routeIs('learning.*') ? 'active' : '' }}"
                   href="{{ route('bhw.learning.index') }}">
                    <i class="bi bi-mortarboard-fill"></i>
                    <span>Learning Materials</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">System</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw.reports*') ? 'active' : '' }}"
                   href="{{ route('bhw.reports.index') }}">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw.notifications*') ? 'active' : '' }}"
                   href="{{ route('bhw.notifications.index') }}">
                    <i class="bi bi-bell-fill"></i>
                    <span>Notifications</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bhw.settings') ? 'active' : '' }}"
                   href="{{ route('bhw.settings') }}">
                    <i class="bi bi-gear-fill"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    @endif

    <div class="sidebar-footer mt-auto">
        <img src="{{ $user->profile_image_url ?? '/images/avatars/avatar-default.svg' }}"
             alt="{{ $user->name }}"
             class="sidebar-footer-avatar"
             onerror="this.onerror=null;this.src='{{ $user->gender === 'male'
                 ? '/images/avatars/avatar-male.svg'
                 : '/images/avatars/avatar-female.svg' }}';">
        <div class="sidebar-footer-info">
            <div class="sidebar-footer-name">{{ $user->name }}</div>
            <div class="sidebar-footer-role" style="color: {{ $roleColor }};">
                <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem; vertical-align: middle;"></i>{{ $roleLabel }}
            </div>
        </div>
    </div>
</nav>
@endif
