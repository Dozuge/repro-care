@extends('user.layout')

@section('title', 'Dashboard - ReproCare')

@section('user-content')

@php
    $hour = now()->hour;
    $timeOfDay = $hour < 12 ? 'Morning' : ($hour < 17 ? 'Afternoon' : 'Evening');

    $tips = [
        ['icon' => 'droplet-fill',     'color' => 'var(--info)',     'title' => 'Stay Hydrated',    'text' => 'Drink at least 8 glasses of water daily for optimal health.'],
        ['icon' => 'apple',             'color' => 'var(--success)',  'title' => 'Eat Healthy',      'text' => 'Include colorful fruits and vegetables in every meal.'],
        ['icon' => 'moon-stars-fill',   'color' => 'var(--accent-violet)', 'title' => 'Get Rest',   'text' => 'Aim for 7–8 hours of quality sleep each night.'],
        ['icon' => 'capsule',           'color' => 'var(--danger)',   'title' => 'Prenatal Vitamins','text' => 'Take your prenatal vitamins daily for a healthy pregnancy.'],
        ['icon' => 'person-walking',    'color' => 'var(--warning)',  'title' => 'Stay Active',      'text' => 'Light walking supports circulation and boosts mood.'],
        ['icon' => 'heart-pulse-fill',  'color' => 'var(--primary)',  'title' => 'Know Your Cycle',  'text' => 'Tracking your cycle helps you understand your body better.'],
        ['icon' => 'sunglasses',        'color' => 'var(--secondary)','title' => 'Manage Stress',   'text' => 'Mindful breathing and relaxation support hormonal balance.'],
    ];
    $todayTip = $tips[now()->dayOfWeek % count($tips)];
@endphp

{{-- ═══════════════════════════════
     PAGE HERO
═══════════════════════════════ --}}
<div class="page-hero fade-in-card" style="background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--accent-pink) 100%);">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">
                Good {{ $timeOfDay }}, {{ auth()->user()->name }}! 🌸
            </div>
            <p class="page-hero-subtitle">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, F j, Y') }}
                &nbsp;·&nbsp; Your health, beautifully tracked.
            </p>
        </div>
        @if(!$activePregnancy)
            <a href="{{ route('user.pregnancies.create') }}" class="btn-hero-primary">
                <i class="bi bi-plus-circle-fill"></i> Start Tracking Pregnancy
            </a>
        @endif
    </div>

    {{-- Summary chips --}}
    <div class="d-flex flex-wrap gap-2 mt-3" style="position:relative;z-index:1;">
        @if($activePregnancy)
            <span class="summary-chip" style="background:rgba(255,255,255,0.2);border-color:rgba(255,255,255,0.4);color:#fff;">
                <i class="bi bi-heart-pulse-fill"></i>
                Week {{ $activePregnancy->aog_weeks }} Pregnant
            </span>
        @endif
        @if($nextPeriod)
            <span class="summary-chip" style="background:rgba(255,255,255,0.2);border-color:rgba(255,255,255,0.4);color:#fff;">
                <i class="bi bi-calendar-heart"></i>
                Next period in {{ round(abs($nextPeriod->diffInDays(now()))) }} days
            </span>
        @endif
        <span class="summary-chip" style="background:rgba(255,255,255,0.2);border-color:rgba(255,255,255,0.4);color:#fff;">
            <i class="bi bi-calendar-check"></i>
            {{ $upcomingCheckups }} upcoming checkup(s)
        </span>
        @if($unreadNotifications > 0)
            <span class="summary-chip" style="background:rgba(255,255,255,0.25);border-color:rgba(255,255,255,0.5);color:#fff;">
                <i class="bi bi-bell-fill"></i>
                {{ $unreadNotifications }} unread notification(s)
            </span>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════
     STAT CARDS
═══════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Pregnancy Card --}}
    @if($activePregnancy)
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-violet fade-in-card">
            <div class="stat-icon"><i class="bi bi-heart-pulse-fill"></i></div>
            <div class="stat-label">Pregnancy Week</div>
            <div class="stat-number" data-count="{{ $activePregnancy->aog_weeks }}">0</div>
            <div class="stat-trend up">
                <i class="bi bi-calendar3"></i>
                EDD: {{ $activePregnancy->edd->format('M d, Y') }}
            </div>
            {{-- Radial progress ring --}}
            <div style="position:absolute;top:1.1rem;right:1.1rem;width:46px;height:46px;">
                <svg viewBox="0 0 36 36" style="transform:rotate(-90deg);">
                    <circle cx="18" cy="18" r="15" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15" fill="none"
                            stroke="rgba(255,255,255,0.9)" stroke-width="3"
                            stroke-dasharray="{{ round(($activePregnancy->aog_weeks / 40) * 94.25, 1) }} 94.25"
                            stroke-linecap="round"/>
                </svg>
            </div>
        </div>
    </div>
    @endif

    {{-- Next Period --}}
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-pink fade-in-card">
            <div class="stat-icon"><i class="bi bi-calendar-heart-fill"></i></div>
            <div class="stat-label">Next Period</div>
            <div class="stat-number" data-count="{{ $nextPeriod ? round(abs($nextPeriod->diffInDays(now()))) : 0 }}">
                {{ $nextPeriod ? '--' : '--' }}
            </div>
            <div class="stat-trend">
                @if($nextPeriod)
                    <i class="bi bi-calendar3"></i> {{ $nextPeriod->format('M d') }}
                @else
                    <i class="bi bi-plus-circle"></i> Start tracking
                @endif
            </div>
        </div>
    </div>

    {{-- Upcoming Checkups --}}
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-cyan fade-in-card">
            <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
            <div class="stat-label">Upcoming Checkups</div>
            <div class="stat-number" data-count="{{ $upcomingCheckups }}">0</div>
            <div class="stat-trend up">
                <i class="bi bi-clock"></i> Scheduled sessions
            </div>
        </div>
    </div>

    {{-- Notifications --}}
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-amber fade-in-card">
            <div class="stat-icon"><i class="bi bi-bell-fill"></i></div>
            <div class="stat-label">Notifications</div>
            <div class="stat-number" data-count="{{ $unreadNotifications }}">0</div>
            <div class="stat-trend">
                <i class="bi bi-envelope"></i> Unread messages
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════
     MAIN CONTENT GRID
═══════════════════════════════ --}}
<div class="row g-4">

    {{-- LEFT COLUMN --}}
    <div class="col-lg-8">

        {{-- Quick Actions --}}
        <div class="card fade-in-card mb-4">
            <div class="card-header">
                <h5 class="mb-0" style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;">
                    <i class="bi bi-lightning-charge-fill me-2" style="color:var(--warning);"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <a href="{{ route('user.menstruation.calendar') }}" class="quick-action-tile">
                            <i class="bi bi-calendar-heart-fill" style="color:var(--secondary);"></i>
                            <span>Cycle Calendar</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('user.pregnancies.index') }}" class="quick-action-tile">
                            <i class="bi bi-heart-pulse-fill" style="color:var(--primary-light);"></i>
                            <span>Pregnancy</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('forum.index') }}" class="quick-action-tile">
                            <i class="bi bi-chat-dots-fill" style="color:var(--info);"></i>
                            <span>Community</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('learning.index') }}" class="quick-action-tile">
                            <i class="bi bi-mortarboard-fill" style="color:var(--success);"></i>
                            <span>Learn</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Pregnancy Details --}}
        @if($activePregnancy)
        <div class="card fade-in-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;">
                    <i class="bi bi-heart-pulse me-2" style="color:var(--secondary);"></i>
                    Pregnancy Journey
                </h5>
                <a href="{{ route('user.pregnancies.index') }}" class="btn btn-sm btn-outline-primary">
                    View Details
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3 text-center">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background:var(--primary-subtle);border:1px solid var(--border);">
                            <div style="font-family:'Plus Jakarta Sans',sans-serif;font-size:1.75rem;font-weight:800;color:var(--primary-light);">
                                {{ $activePregnancy->formatted_aog }}
                            </div>
                            <div style="font-size:0.8rem;color:var(--text-muted);margin-top:0.25rem;">Age of Gestation</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);">
                            <div style="font-family:'Plus Jakarta Sans',sans-serif;font-size:1.2rem;font-weight:800;color:#34d399;">
                                {{ $activePregnancy->edd->format('M d, Y') }}
                            </div>
                            <div style="font-size:0.8rem;color:var(--text-muted);margin-top:0.25rem;">Expected Due Date</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background:rgba(6,182,212,0.08);border:1px solid rgba(6,182,212,0.2);">
                            <div style="font-family:'Plus Jakarta Sans',sans-serif;font-size:1.75rem;font-weight:800;color:#22d3ee;">
                                {{ $activePregnancy->gravida }} / {{ $activePregnancy->para }}
                            </div>
                            <div style="font-size:0.8rem;color:var(--text-muted);margin-top:0.25rem;">Gravida / Para</div>
                        </div>
                    </div>
                </div>

                {{-- Trimester Progress Bar --}}
                @php
                    $weeks    = $activePregnancy->aog_weeks;
                    $pct      = min(round(($weeks / 40) * 100), 100);
                    $trimester = $weeks <= 12 ? '1st Trimester' : ($weeks <= 27 ? '2nd Trimester' : '3rd Trimester');
                    $trimColor = $weeks <= 12 ? 'var(--primary)' : ($weeks <= 27 ? 'var(--success)' : 'var(--warning)');
                @endphp
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span style="font-size:0.82rem;font-weight:600;color:var(--text-muted);">{{ $trimester }}</span>
                        <span style="font-size:0.82rem;font-weight:700;color:var(--text);">Week {{ $weeks }} / 40</span>
                    </div>
                    <div style="height:8px;background:var(--bg-card2);border-radius:10px;overflow:hidden;border:1px solid var(--border);">
                        <div style="width:{{ $pct }}%;height:100%;background:linear-gradient(90deg,{{ $trimColor }},var(--primary-light));border-radius:10px;transition:width 1s ease;"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Recent Activity --}}
        <div class="card fade-in-card">
            <div class="card-header">
                <h5 class="mb-0" style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;">
                    <i class="bi bi-activity me-2" style="color:var(--primary-light);"></i>
                    Recent Activity
                </h5>
            </div>
            <div class="card-body">
                @php
                    $recentCheckups = auth()->user()->checkups()->latest()->take(3)->get();
                    $recentRecords  = auth()->user()->cycles()->latest()->take(2)->get();
                @endphp

                @if($recentCheckups->count() > 0 || $recentRecords->count() > 0)
                    <ul class="activity-timeline">
                        @foreach($recentCheckups as $c)
                            <li class="timeline-item">
                                <div class="timeline-dot dot-primary">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div class="timeline-content">
                                    <div style="font-weight:600;font-size:0.875rem;">
                                        Checkup with {{ $c->midwife->name ?? 'Midwife' }}
                                    </div>
                                    <span class="timeline-time">
                                        {{ $c->scheduled_date->format('F j, Y') }}
                                        &nbsp;·&nbsp;
                                        <span class="status-{{ strtolower($c->status) }}">{{ ucfirst($c->status) }}</span>
                                    </span>
                                </div>
                            </li>
                        @endforeach

                        @foreach($recentRecords as $r)
                            <li class="timeline-item">
                                <div class="timeline-dot dot-danger">
                                    <i class="bi bi-droplet-fill"></i>
                                </div>
                                <div class="timeline-content">
                                    <div style="font-weight:600;font-size:0.875rem;">Period Record</div>
                                    <span class="timeline-time">
                                        {{ $r->period_start_date->format('M d') }} –
                                        {{ $r->period_end_date ? $r->period_end_date->format('M d') : 'Present' }}
                                        ({{ $r->duration }} days)
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty-state">
                        <i class="bi bi-clock-history empty-state-icon"></i>
                        <h6>No Recent Activity</h6>
                        <p>Your activity will appear here once you start tracking.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN --}}
    <div class="col-lg-4">

        {{-- Daily Health Tip --}}
        <div class="card fade-in-card mb-4">
            <div class="card-header">
                <h5 class="mb-0" style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;">
                    <i class="bi bi-lightbulb-fill me-2" style="color:var(--warning);"></i>
                    Daily Health Tip
                </h5>
            </div>
            <div class="card-body text-center py-4">
                <div style="width:70px;height:70px;border-radius:20px;background:var(--primary-subtle);border:1px solid var(--border-glass);display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;font-size:2rem;">
                    <i class="bi bi-{{ $todayTip['icon'] }}" style="color:{{ $todayTip['color'] }};"></i>
                </div>
                <h6 style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;margin-bottom:0.5rem;">
                    {{ $todayTip['title'] }}
                </h6>
                <p style="font-size:0.875rem;color:var(--text-muted);margin:0;line-height:1.6;">
                    {{ $todayTip['text'] }}
                </p>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="card fade-in-card mb-4">
            <div class="card-header">
                <h5 class="mb-0" style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;">
                    <i class="bi bi-link-45deg me-2" style="color:var(--primary-light);"></i>
                    Quick Links
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <a href="{{ route('user.menstruation.calendar') }}"
                       class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
                        <div style="width:34px;height:34px;border-radius:10px;background:rgba(244,63,142,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-calendar-heart-fill" style="color:var(--secondary);"></i>
                        </div>
                        <span style="font-weight:500;">Cycle Calendar</span>
                        <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);font-size:0.8rem;"></i>
                    </a>
                    <a href="{{ route('user.menstruation.statistics') }}"
                       class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
                        <div style="width:34px;height:34px;border-radius:10px;background:rgba(16,185,129,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-graph-up" style="color:var(--success);"></i>
                        </div>
                        <span style="font-weight:500;">Cycle Statistics</span>
                        <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);font-size:0.8rem;"></i>
                    </a>
                    <a href="{{ route('user.health-records') }}"
                       class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
                        <div style="width:34px;height:34px;border-radius:10px;background:rgba(245,158,11,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-clipboard-pulse-fill" style="color:var(--warning);"></i>
                        </div>
                        <span style="font-weight:500;">Health Records</span>
                        <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);font-size:0.8rem;"></i>
                    </a>
                    <a href="{{ route('user.checkups') }}"
                       class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
                        <div style="width:34px;height:34px;border-radius:10px;background:rgba(6,182,212,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-clipboard-heart-fill" style="color:var(--info);"></i>
                        </div>
                        <span style="font-weight:500;">My Checkups</span>
                        <i class="bi bi-chevron-right ms-auto" style="color:var(--text-muted);font-size:0.8rem;"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Community Preview --}}
        @php
            $recentForumPosts = \App\Models\ForumPost::active()->latest()->take(3)->get();
        @endphp
        @if($recentForumPosts->count() > 0)
        <div class="card fade-in-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;">
                    <i class="bi bi-chat-dots-fill me-2" style="color:var(--primary-light);"></i>
                    Community
                </h5>
                <a href="{{ route('forum.index') }}"
                   style="font-size:0.8rem;color:var(--primary-light);text-decoration:none;font-weight:600;">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($recentForumPosts as $post)
                        <div class="list-group-item py-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent-violet));display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#fff;flex-shrink:0;">
                                    {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                </div>
                                <span style="font-size:0.8rem;font-weight:600;color:var(--text);">{{ $post->user->name }}</span>
                            </div>
                            <p style="font-size:0.82rem;color:var(--text-muted);margin:0;line-height:1.5;">
                                {{ \Illuminate\Support\Str::limit($post->content, 70) }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection
