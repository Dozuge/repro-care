<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('rc_theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();

        // Reload pages restored from browser history so edited list data does not stay stale.
        window.addEventListener('pageshow', function (event) {
            const navigationEntry = performance.getEntriesByType('navigation')[0];
            const isHistoryRestore = event.persisted || navigationEntry?.type === 'back_forward';

            if (isHistoryRestore) {
                window.location.reload();
            }
        });
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ReproCare - Maternal Health System')</title>
    <meta name="description" content="ReproCare - Comprehensive Maternal & Reproductive Health Management System">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/brand/reprocare-logo.svg') }}">
    <link rel="shortcut icon" href="{{ asset('images/brand/reprocare-logo.svg') }}">

    <!-- Google Fonts: Plus Jakarta Sans + Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @stack('styles')

    <style>
        /* ============================================================
           REPROCARE DESIGN SYSTEM v2 — #da36ff Primary Theme
           Supports [data-theme="dark"] and [data-theme="light"]
           ============================================================ */

        /* ── 1. CSS VARIABLES ── */
        :root {
            /* Brand Colors */
            --primary:           #da36ff;
            --primary-dark:      #b020d4;
            --primary-light:     #e75fff;
            --primary-glow:      rgba(218, 54, 255, 0.35);
            --primary-subtle:    rgba(218, 54, 255, 0.10);
            --secondary:         #f43f8e;
            --accent-violet:     #9b36ff;
            --accent-pink:       #ff36b0;

            /* Semantic Colors */
            --success:   #10b981;
            --warning:   #f59e0b;
            --danger:    #ef4444;
            --info:      #06b6d4;

            /* Sidebar */
            --sidebar-w: 265px;
            --sidebar-collapsed-w: 88px;

            /* Transitions */
            --transition-fast:   0.15s ease;
            --transition-base:   0.25s ease;
            --transition-slow:   0.4s ease;
        }

        /* ── DARK MODE (default) ── */
        [data-theme="dark"] {
            --bg-main:       #08040f;
            --bg-card:       #130a22;
            --bg-card2:      #1d0f33;
            --bg-input:      #27104a;
            --bg-glass:      rgba(30, 10, 50, 0.65);
            --border:        rgba(218, 54, 255, 0.24);  /* ↑ more visible */
            --border-glass:  rgba(218, 54, 255, 0.28);
            --text:          #f0e8ff;                   /* white with purple tint */
            --text-muted:    #c4a3e0;                   /* ↑ brighter muted text */
            --text-dim:      #8a6aaa;                   /* very muted (timestamps) */
            --shadow-sm:     0 2px 12px rgba(0,0,0,0.5);
            --shadow-md:     0 8px 40px rgba(0,0,0,0.55);
            --shadow-glow:   0 0 50px rgba(218,54,255,0.4);
            --nav-bg:        rgba(13, 6, 22, 0.92);
            --sidebar-bg:    linear-gradient(180deg, #130a22 0%, #08040f 100%);
            --skeleton-from: #1d0f33;
            --skeleton-to:   #2a1545;
            --row-alt:       rgba(218, 54, 255, 0.04);  /* alternating table rows */
            --row-hover:     rgba(218, 54, 255, 0.10);
            --input-border:  rgba(218, 54, 255, 0.35);
            --focus-ring:    rgba(218, 54, 255, 0.35);
            --placeholder:   rgba(196, 163, 224, 0.50);
        }

        /* ── LIGHT MODE ── */
        [data-theme="light"] {
            --bg-main:       #f4eeff;
            --bg-card:       #ffffff;
            --bg-card2:      #f0e6ff;
            --bg-input:      #faf6ff;
            --bg-glass:      rgba(255, 255, 255, 0.82);
            --border:        rgba(139, 77, 255, 0.22);  /* ↑ more visible on white */
            --border-glass:  rgba(139, 77, 255, 0.28);
            --text:          #1a0630;
            --text-muted:    #5c4080;                   /* ↑ darker = better contrast */
            --text-dim:      #9176b8;
            --shadow-sm:     0 2px 8px rgba(218,54,255,0.09);
            --shadow-md:     0 8px 30px rgba(218,54,255,0.16);
            --shadow-glow:   0 0 40px rgba(218,54,255,0.22);
            --nav-bg:        rgba(255, 255, 255, 0.92);
            --sidebar-bg:    linear-gradient(180deg, #fdfaff 0%, #f1e8ff 100%);
            --skeleton-from: #ede8f7;
            --skeleton-to:   #f5f0ff;
            --row-alt:       rgba(139, 77, 255, 0.04);
            --row-hover:     rgba(139, 77, 255, 0.08);
            --input-border:  rgba(139, 77, 255, 0.30);
            --focus-ring:    rgba(218, 54, 255, 0.25);
            --placeholder:   rgba(92, 64, 128, 0.45);
        }

        /* ── 2. GLOBAL RESET & BASE ── */
        *, *::before, *::after { box-sizing: border-box; }

        html {
            transition: background-color 0s;
        }

        body {
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            background-color: var(--bg-main);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: 60px;
            transition: background-color 0s, color var(--transition-base);
        }

        /* ── 3. SCROLLBAR ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: var(--bg-main); }
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
            opacity: 0.6;
        }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary-light); }

        /* ── 4. NAVBAR ── */
        .navbar {
            background: var(--nav-bg) !important;
            border-bottom: 1px solid var(--border-glass);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            padding: 0.55rem 1.5rem;
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1050;
            box-shadow: var(--shadow-sm);
            transition: background var(--transition-slow), border-color var(--transition-base);
        }

        .navbar-brand {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--text) !important;
            display: flex;
            align-items: center;
            gap: 0.55rem;
            letter-spacing: -0.5px;
            text-decoration: none;
        }

        .navbar-brand .brand-icon {
            width: 40px; height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(255,255,255,0.6);
            box-shadow: 0 10px 24px rgba(122, 56, 201, 0.18);
            flex-shrink: 0;
            transition: box-shadow var(--transition-base), transform var(--transition-base);
        }
        .navbar-brand .brand-logo-image {
            width: 26px;
            height: 26px;
            object-fit: contain;
        }

        .navbar-brand:hover .brand-icon {
            box-shadow: 0 14px 30px rgba(122, 56, 201, 0.24);
            transform: scale(1.05);
        }

        .navbar .nav-link {
            color: var(--text-muted) !important;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.45rem 0.7rem !important;
            border-radius: 8px;
            transition: all var(--transition-fast);
        }
        .navbar .nav-link:hover {
            color: var(--text) !important;
            background: var(--primary-subtle);
        }

        /* Role Badges in Navbar */
        .role-chip {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 0.28em 0.9em;
            border-radius: 20px;
        }
        .role-chip-midwife {
            background: linear-gradient(135deg, var(--primary), var(--accent-violet));
            color: #fff;
            box-shadow: 0 2px 10px var(--primary-glow);
        }
        .role-chip-bhw {
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            color: #fff;
            box-shadow: 0 2px 10px rgba(6,182,212,0.35);
        }
        .role-chip-bhw-president {
            background: linear-gradient(135deg, #7c3aed, #c026d3);
            color: #fff;
            box-shadow: 0 2px 10px rgba(124,58,237,0.35);
        }
        .role-chip-user {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: #fff;
            box-shadow: 0 2px 10px rgba(244,63,142,0.35);
        }

        /* Notification Bell */
        .notif-bell-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .notif-bell-wrap .notif-dot {
            position: absolute;
            top: 4px; right: 4px;
            width: 8px; height: 8px;
            background: var(--danger);
            border-radius: 50%;
            border: 2px solid var(--bg-main);
            animation: pulse-dot 1.8s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50%       { transform: scale(1.4); opacity: 0.7; }
        }

        /* User Avatar */
        .nav-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
            box-shadow: 0 0 0 3px var(--primary-subtle);
            transition: box-shadow var(--transition-base), transform var(--transition-base);
        }
        .nav-avatar:hover {
            box-shadow: 0 0 0 4px var(--primary-glow);
            transform: scale(1.05);
        }

        /* Dropdown */
        .navbar .dropdown-menu {
            background: var(--bg-card2);
            border: 1px solid var(--border-glass);
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(20px);
            padding: 0.5rem;
            min-width: 210px;
            transition: background var(--transition-slow);
        }
        .navbar .dropdown-item {
            color: var(--text-muted);
            border-radius: 10px;
            padding: 0.55rem 0.85rem;
            font-size: 0.875rem;
            transition: all var(--transition-fast);
        }
        .navbar .dropdown-item:hover {
            background: var(--primary-subtle);
            color: var(--text);
        }
        .navbar .dropdown-divider { border-color: var(--border); margin: 0.3rem 0; }
        .navbar .dropdown-item.text-danger:hover { background: rgba(239,68,68,0.1); color: #f87171 !important; }

        /* Mobile toggler */
        .navbar-toggler {
            border: 1px solid var(--border) !important;
            color: var(--text-muted);
            border-radius: 8px;
            padding: 0.35rem 0.5rem;
            transition: all var(--transition-fast);
        }
        .navbar-toggler:hover { background: var(--primary-subtle); }

        /* ── 5. SIDEBAR ── */
        .sidebar {
            position: fixed;
            top: 60px; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            z-index: 1020;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1), background var(--transition-slow), border-color var(--transition-base);
            padding: 1rem 0 4rem;
            display: flex;
            flex-direction: column;
        }
        .sidebar::-webkit-scrollbar { width: 2px; }
        body.sidebar-collapsed .sidebar {
            width: var(--sidebar-collapsed-w);
        }
        body.sidebar-collapsed .sidebar-portal-label,
        body.sidebar-collapsed .sidebar-section-label,
        body.sidebar-collapsed .sidebar .nav-link span,
        body.sidebar-collapsed .sidebar-footer-info {
            display: none;
        }
        body.sidebar-collapsed .sidebar .nav-link {
            justify-content: center;
            padding-left: 0.9rem;
            padding-right: 0.9rem;
        }
        body.sidebar-collapsed .sidebar .nav-link:hover {
            padding-left: 0.9rem;
        }
        body.sidebar-collapsed .sidebar .sub-menu {
            padding-left: 0;
        }
        body.sidebar-collapsed .sidebar-footer {
            justify-content: center;
        }

        /* Sidebar portal label */
        .sidebar-portal-label {
            padding: 0.6rem 1.2rem 0.8rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 1.8px;
            background: linear-gradient(135deg, var(--primary), var(--accent-violet));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Sidebar section labels */
        .sidebar-section-label {
            padding: 1rem 1.3rem 0.35rem;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            opacity: 0.6;
        }

        /* Sidebar nav links */
        .sidebar .nav-link {
            color: var(--text-muted);
            padding: 0.62rem 1.2rem;
            margin: 0.06rem 0.65rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            transition: background var(--transition-fast), color var(--transition-fast), padding-left var(--transition-fast);
            position: relative;
            text-decoration: none;
        }
        .sidebar .nav-link i {
            font-size: 1rem;
            width: 1.2rem;
            text-align: center;
            flex-shrink: 0;
        }
        .sidebar .nav-link:hover {
            background: var(--primary-subtle);
            color: var(--text);
            padding-left: calc(1.2rem + 4px);
        }
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary-subtle) 0%, rgba(155,54,255,0.12) 100%);
            color: var(--primary-light);
            font-weight: 600;
        }
        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: -0.65rem;
            top: 18%; height: 64%;
            width: 3px;
            background: linear-gradient(180deg, var(--primary), var(--accent-violet));
            border-radius: 0 3px 3px 0;
            animation: activeBarGrow 0.3s ease;
        }
        @keyframes activeBarGrow {
            from { height: 0; top: 50%; }
            to   { height: 64%; top: 18%; }
        }

        /* Submenu */
        .sidebar .sub-menu { padding: 0.2rem 0 0.2rem 2rem; }
        .sidebar .sub-menu .nav-link {
            padding: 0.45rem 0.75rem;
            margin: 0.04rem 0.4rem;
            font-size: 0.82rem;
            border-radius: 10px;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            margin-top: auto;
            padding: 0.75rem 1rem;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .sidebar-footer-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
            box-shadow: 0 0 8px var(--primary-glow);
            flex-shrink: 0;
        }
        .sidebar-footer-info { flex: 1; min-width: 0; }
        .sidebar-footer-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-footer-role {
            font-size: 0.7rem;
            color: var(--primary-light);
            text-transform: capitalize;
            font-weight: 500;
        }

        /* ── 6. LAYOUT WRAPPER & MAIN CONTENT ── */
        .layout-wrapper {
            display: flex;
            min-height: calc(100vh - 60px);
            width: 100%;
        }
        .main-content {
            margin-left: var(--sidebar-w);
            padding: 1.75rem 2rem 4.5rem;
            flex: 1;
            min-height: calc(100vh - 60px);
            width: calc(100% - var(--sidebar-w));
            max-width: calc(100% - var(--sidebar-w));
            box-sizing: border-box;
            overflow-x: hidden;
            transition: margin-left 0.3s cubic-bezier(0.4,0,0.2,1), width 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        body.sidebar-collapsed .main-content {
            margin-left: var(--sidebar-collapsed-w);
            width: calc(100% - var(--sidebar-collapsed-w));
            max-width: calc(100% - var(--sidebar-collapsed-w));
        }

        .main-content > * {
            max-width: 100%;
            overflow-x: auto;
        }

        /* ── 7. CARDS ── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            transition: background var(--transition-slow), border-color var(--transition-base), box-shadow var(--transition-base), transform var(--transition-base);
        }
        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
            border-color: var(--border-glass);
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.25rem;
            color: var(--text);
            transition: border-color var(--transition-base);
        }
        .card-body { color: var(--text); }
        .card-footer {
            background: transparent;
            border-top: 1px solid var(--border);
            transition: border-color var(--transition-base);
        }

        /* Glass Card */
        .glass-card {
            background: var(--bg-glass);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            transition: background var(--transition-slow), box-shadow var(--transition-base);
        }

        /* Stat Card */
        .stat-card {
            border: none !important;
            border-radius: 20px;
            padding: 1.4rem;
            position: relative;
            overflow: hidden;
            cursor: default;
            box-shadow: var(--shadow-md);
            transition: transform var(--transition-base), box-shadow var(--transition-base);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-glow);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, transparent 60%);
            pointer-events: none;
        }
        .stat-card .stat-icon {
            width: 52px; height: 52px;
            background: rgba(255,255,255,0.18);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
            backdrop-filter: blur(8px);
            margin-bottom: 1rem;
        }
        .stat-card .stat-label {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: rgba(255,255,255,0.72);
            margin-bottom: 0.3rem;
        }
        .stat-card .stat-number {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 2.4rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .stat-card .stat-trend {
            font-size: 0.78rem;
            font-weight: 600;
            color: rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            gap: 0.2rem;
        }
        .stat-card .stat-trend.up { color: #a7f3d0; }
        .stat-card .stat-trend.danger { color: #fca5a5; }

        /* Stat Gradients */
        .stat-purple  { background: linear-gradient(135deg, #da36ff 0%, #9b36ff 100%); }
        .stat-rose    { background: linear-gradient(135deg, #f43f8e 0%, #da36ff 100%); }
        .stat-cyan    { background: linear-gradient(135deg, #06b6d4 0%, #0ea5e9 100%); }
        .stat-amber   { background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); }
        .stat-green   { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .stat-lime    { background: linear-gradient(135deg, #84cc16 0%, #65a30d 100%); }
        .stat-violet  { background: linear-gradient(135deg, #9b36ff 0%, #da36ff 100%); }
        .stat-pink    { background: linear-gradient(135deg, #ff36b0 0%, #f43f8e 100%); }
        .stat-indigo  { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
        .stat-danger  { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

        /* ── 8. PAGE HERO ── */
        .page-hero {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--accent-pink) 100%);
            border-radius: 20px;
            padding: 1.75rem 2rem;
            margin-bottom: 1.75rem;
            position: relative;
            overflow: hidden;
        }
        .page-hero::before {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
            top: -100px; right: -80px;
            pointer-events: none;
        }
        .page-hero::after {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            bottom: -60px; left: 30%;
            pointer-events: none;
        }
        .page-hero-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 0.25rem;
            position: relative;
        }
        .page-hero-subtitle {
            font-size: 0.875rem;
            color: rgba(255,255,255,0.75);
            margin: 0;
            position: relative;
        }

        /* ── 9. BUTTONS ── */
        .btn {
            transition: transform var(--transition-fast), box-shadow var(--transition-fast), background var(--transition-fast);
            position: relative;
            overflow: hidden;
        }
        .btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.18) 50%, transparent 100%);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }
        .btn:hover::after { transform: translateX(100%); }
        .btn:active { transform: scale(0.97) !important; }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.55rem 1.25rem;
            color: #fff;
            box-shadow: 0 4px 16px var(--primary-glow);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px var(--primary-glow);
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            color: #fff;
        }

        .btn-outline-primary {
            border: 1.5px solid var(--primary);
            color: var(--primary-light);
            background: transparent;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.55rem 1.25rem;
            transition: all var(--transition-base);
        }
        .btn-outline-primary:hover {
            background: var(--primary-subtle);
            border-color: var(--primary-light);
            color: var(--primary-light);
        }

        .btn-outline-secondary {
            border: 1.5px solid var(--border);
            color: var(--text-muted);
            background: transparent;
            border-radius: 12px;
            font-size: 0.875rem;
            padding: 0.55rem 1.25rem;
            transition: all var(--transition-base);
        }
        .btn-outline-secondary:hover {
            background: var(--bg-card2);
            border-color: var(--primary);
            color: var(--text);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            color: #fff;
            box-shadow: 0 4px 14px rgba(16,185,129,0.35);
        }
        .btn-success:hover { color: #fff; }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            color: #fff;
            box-shadow: 0 4px 14px rgba(239,68,68,0.35);
        }
        .btn-danger:hover { color: #fff; }

        .btn-info {
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            color: #fff;
            box-shadow: 0 4px 14px rgba(6,182,212,0.35);
        }
        .btn-info:hover { color: #fff; }

        .btn-sm { padding: 0.35rem 0.85rem; font-size: 0.8rem; border-radius: 10px; }

        /* Outline light → matches both modes */
        .btn-outline-light {
            border: 1.5px solid rgba(255,255,255,0.35);
            color: rgba(255,255,255,0.9);
            border-radius: 12px;
            font-size: 0.8rem;
            background: transparent;
        }
        .btn-outline-light:hover { background: rgba(255,255,255,0.12); color: #fff; border-color: rgba(255,255,255,0.6); }

        /* Hero button */
        .btn-hero-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.4rem;
            background: rgba(255,255,255,0.2);
            border: 1.5px solid rgba(255,255,255,0.4);
            border-radius: 12px;
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all var(--transition-base);
            backdrop-filter: blur(8px);
        }
        .btn-hero-primary:hover {
            background: rgba(255,255,255,0.32);
            color: #fff;
            transform: translateY(-2px);
        }

        /* ── 10. FORMS ── */
        /* ── FORMS & INPUTS — polished visibility ── */
        .form-control, .form-select {
            background: var(--bg-input);
            border: 1.5px solid var(--input-border, var(--border));
            color: var(--text);
            border-radius: 12px;
            font-size: 0.9rem;
            padding: 0.6rem 0.95rem;
            transition: background var(--transition-slow), border-color var(--transition-base),
                        box-shadow var(--transition-base), color var(--transition-base);
        }
        .form-control:focus, .form-select:focus {
            background: var(--bg-input);
            border-color: var(--primary);
            color: var(--text);
            box-shadow: 0 0 0 3px var(--focus-ring, var(--primary-glow));
            outline: none;
        }
        /* ↑ more visible placeholder — meets WCAG AA */
        .form-control::placeholder { color: var(--placeholder, var(--text-muted)); opacity: 1; }
        textarea.form-control::placeholder { color: var(--placeholder, var(--text-muted)); opacity: 1; }
        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text);            /* always full-brightness label */
            margin-bottom: 0.45rem;
            letter-spacing: 0.01em;
        }
        .form-text  { color: var(--text-muted); font-size: 0.8rem; }
        .form-select option { background: var(--bg-card2); color: var(--text); }

        .input-group-text {
            background: var(--bg-input);
            border: 1.5px solid var(--input-border, var(--border));
            color: var(--text-muted);
            transition: background var(--transition-slow), border-color var(--transition-base);
        }
        .form-check-input {
            background-color: var(--bg-input);
            border-color: var(--border);
            width: 1.1em; height: 1.1em;
            transition: background var(--transition-base), border-color var(--transition-base);
        }
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 0 0 2px var(--focus-ring);
        }
        .form-check-label { color: var(--text); font-size: 0.875rem; }

        /* Custom RC Switch (for settings) */
        .rc-switch { display: inline-flex; align-items: center; gap: 0.6rem; cursor: pointer; }
        .rc-switch input { display: none; }
        .rc-track {
            width: 52px; height: 28px;
            background: var(--bg-card2);
            border: 1.5px solid var(--border);
            border-radius: 100px;
            position: relative;
            transition: background 0.3s ease, border-color 0.3s ease;
        }
        .rc-track .rc-thumb {
            position: absolute;
            width: 20px; height: 20px;
            background: var(--text-muted);
            border-radius: 50%;
            top: 3px; left: 3px;
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), background 0.25s ease;
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        }
        .rc-switch input:checked ~ .rc-track { background: var(--primary); border-color: var(--primary); }
        .rc-switch input:checked ~ .rc-track .rc-thumb {
            transform: translateX(24px);
            background: #fff;
            box-shadow: 0 2px 8px var(--primary-glow);
        }

        /* ── 11. ALERTS ── */
        .alert {
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            border: 1px solid;
            padding: 1rem 1.1rem;
            font-size: 0.92rem;
            line-height: 1.55;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
            transition: background var(--transition-slow), border-color var(--transition-base), transform var(--transition-base);
        }
        .alert::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            border-radius: 18px 0 0 18px;
            background: currentColor;
            opacity: 0.7;
        }
        .alert-success { background: linear-gradient(135deg, rgba(16,185,129,0.16), rgba(16,185,129,0.08)); border-color: rgba(16,185,129,0.28); color: #15803d; }
        .alert-danger  { background: linear-gradient(135deg, rgba(239,68,68,0.16), rgba(239,68,68,0.08)); border-color: rgba(239,68,68,0.28); color: #dc2626; }
        .alert-info    { background: linear-gradient(135deg, rgba(6,182,212,0.16), rgba(6,182,212,0.08)); border-color: rgba(6,182,212,0.28); color: #0f766e; }
        .alert-warning { background: linear-gradient(135deg, rgba(245,158,11,0.16), rgba(245,158,11,0.08)); border-color: rgba(245,158,11,0.28); color: #b45309; }
        .alert-primary { background: linear-gradient(135deg, rgba(218,54,255,0.16), rgba(155,54,255,0.08)); border-color: rgba(218,54,255,0.24); color: #9333ea; }
        [data-theme="dark"] .alert-success { color: #6ee7b7; }
        [data-theme="dark"] .alert-danger { color: #fca5a5; }
        [data-theme="dark"] .alert-info { color: #67e8f9; }
        [data-theme="dark"] .alert-warning { color: #fcd34d; }
        [data-theme="dark"] .alert-primary { color: #f0abfc; }
        .alert .btn-close { filter: invert(0.45); }
        .app-confirm-modal .modal-content {
            border: 1px solid var(--border);
            border-radius: 22px;
            background: var(--bg-card);
            box-shadow: var(--shadow-md);
        }
        .app-confirm-modal .modal-header,
        .app-confirm-modal .modal-footer {
            border-color: var(--border);
        }
        .app-confirm-icon {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, rgba(218,54,255,0.16), rgba(244,63,142,0.16));
            color: var(--primary);
            font-size: 1.35rem;
        }

        /* ── 12. BADGES ── */
        .badge {
            font-weight: 700;
            font-size: 0.72rem;
            border-radius: 20px;           /* pill style */
            padding: 0.32em 0.75em;
            letter-spacing: 0.3px;
        }
        /* Semantic badge overrides */
        .badge.bg-primary  { background: var(--primary) !important; }
        .badge.bg-secondary{ background: var(--bg-card2) !important; color: var(--text) !important; border: 1px solid var(--border); }
        .badge.bg-success  { background: rgba(16,185,129,0.22) !important; color: #34d399 !important; }
        .badge.bg-danger   { background: rgba(239,68,68,0.22) !important;  color: #f87171 !important; }
        .badge.bg-warning  { background: rgba(245,158,11,0.22) !important; color: #fbbf24 !important; }
        .badge.bg-info     { background: rgba(6,182,212,0.22) !important;  color: #22d3ee !important; }

        /* Status pill badges — readable in both modes */
        .status-scheduled { background: rgba(6,182,212,0.18);   color: #22d3ee; border-radius: 20px; padding: 0.25rem 0.8rem; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; }
        .status-completed { background: rgba(16,185,129,0.18);  color: #34d399; border-radius: 20px; padding: 0.25rem 0.8rem; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; }
        .status-missed    { background: rgba(239,68,68,0.18);   color: #f87171; border-radius: 20px; padding: 0.25rem 0.8rem; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; }
        .status-pending   { background: rgba(245,158,11,0.18);  color: #fbbf24; border-radius: 20px; padding: 0.25rem 0.8rem; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; }
        .status-active    { background: rgba(16,185,129,0.18);  color: #34d399; border-radius: 20px; padding: 0.25rem 0.8rem; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; }
        .status-cancelled { background: rgba(139,92,246,0.18);  color: #c4b5fd; border-radius: 20px; padding: 0.25rem 0.8rem; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; }

        /* ── 13. TABLES — card-style, modern ── */
        .table {
            color: var(--text);
            --bs-table-bg: transparent;
            --bs-table-striped-bg: var(--row-alt);
            --bs-table-hover-bg: var(--row-hover);
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-bordered { border-color: var(--border); }
        .table td, .table th {
            border-color: var(--border);
            padding: 0.9rem 1.1rem;
            vertical-align: middle;
            transition: background var(--transition-fast), color var(--transition-base);
            font-size: 0.875rem;
        }
        .table tbody td { color: var(--text); }   /* ← explicit: never invisible */
        .table-hover tbody tr { transition: background var(--transition-fast); cursor: default; }
        .table-hover tbody tr:hover td { background: var(--row-hover) !important; }
        .table-striped tbody tr:nth-of-type(odd) td { background: var(--row-alt); }
        .table thead th {
            background: var(--bg-card2);
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 2px solid var(--border);
            white-space: nowrap;
        }
        /* ── Card-style table wrapper — wrap any table in .table-card ── */
        .table-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: background var(--transition-slow);
        }
        .table-card .table { margin: 0; }
        .table-card .table thead th:first-child { border-radius: 0; }
        .table-card .table tbody tr:last-child td { border-bottom: none; }
        /* Sticky header (opt-in via .table-sticky-head) */
        .table-sticky-head thead th { position: sticky; top: 0; z-index: 2; }

        /* ── 14. LIST GROUPS ── */
        .list-group-item {
            background: transparent;
            border-color: var(--border);
            color: var(--text);            /* ↑ explicit to prevent invisible text */
            font-size: 0.875rem;
            transition: background var(--transition-fast), color var(--transition-base);
        }
        .list-group-item-action:hover {
            background: var(--row-hover);
            color: var(--text);
            border-color: var(--primary);
        }
        .list-group-flush .list-group-item:first-child { border-top: none; }
        /* Flush list inside a card — removes outer border */
        .list-group-flush .list-group-item { border-left: none; border-right: none; }

        /* ── 15. MODALS ── */
        .modal-content {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            box-shadow: var(--shadow-md);
            color: var(--text);
            transition: background var(--transition-slow);
        }
        .modal-header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            border-bottom: none;
            border-radius: 19px 19px 0 0;
            padding: 1.1rem 1.4rem;
            color: #fff;
        }
        .modal-header .btn-close { filter: invert(1) brightness(2); opacity: 0.8; }
        .modal-body { padding: 1.4rem; }
        .modal-footer {
            background: var(--bg-card2);
            border-top: 1px solid var(--border);
            border-radius: 0 0 19px 19px;
            padding: 0.85rem 1.4rem;
        }
        .modal-title { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; font-size: 1.05rem; }

        /* ── 16. SKELETON LOADERS ── */
        .skeleton {
            background: linear-gradient(90deg, var(--skeleton-from) 25%, var(--skeleton-to) 50%, var(--skeleton-from) 75%);
            background-size: 200% 100%;
            animation: skeleton-shimmer 1.6s infinite;
            border-radius: 10px;
        }
        @keyframes skeleton-shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* ── 17. EMPTY STATES ── */
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
        }
        .empty-state-icon {
            font-size: 3.5rem;
            color: var(--text-muted);
            opacity: 0.4;
            margin-bottom: 1rem;
            display: block;
        }
        .empty-state h6 {
            color: var(--text);
            font-weight: 600;
            margin-bottom: 0.4rem;
        }
        .empty-state p {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-bottom: 1.2rem;
        }

        /* ── 18. ACTIVITY TIMELINE ── */
        .activity-timeline { list-style: none; padding: 0; margin: 0; }
        .timeline-item {
            display: flex;
            gap: 0.9rem;
            padding-bottom: 1.1rem;
            position: relative;
        }
        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 15px; top: 28px;
            width: 1px;
            bottom: 0;
            background: var(--border);
        }
        .timeline-dot {
            width: 30px; height: 30px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem;
            color: #fff;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .dot-primary { background: linear-gradient(135deg, var(--primary), var(--accent-violet)); box-shadow: 0 2px 8px var(--primary-glow); }
        .dot-success { background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 2px 8px rgba(16,185,129,0.35); }
        .dot-warning { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 2px 8px rgba(245,158,11,0.35); }
        .dot-danger  { background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 2px 8px rgba(239,68,68,0.35); }
        .dot-info    { background: linear-gradient(135deg, #06b6d4, #0ea5e9); box-shadow: 0 2px 8px rgba(6,182,212,0.35); }

        .timeline-content {
            flex: 1;
            background: var(--bg-card2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.65rem 0.9rem;
            font-size: 0.85rem;
            transition: background var(--transition-slow);
        }
        .timeline-time {
            font-size: 0.75rem;
            color: var(--text-muted);
            display: block;
            margin-top: 0.2rem;
        }

        /* ── 19. FADE-IN ANIMATION ── */
        .fade-in-card {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        .fade-in-card.visible { opacity: 1; transform: translateY(0); }
        .fade-in-card:nth-child(1) { transition-delay: 0.04s; }
        .fade-in-card:nth-child(2) { transition-delay: 0.08s; }
        .fade-in-card:nth-child(3) { transition-delay: 0.12s; }
        .fade-in-card:nth-child(4) { transition-delay: 0.16s; }
        .fade-in-card:nth-child(5) { transition-delay: 0.20s; }
        .fade-in-card:nth-child(6) { transition-delay: 0.24s; }

        /* ── 20. PAGE TITLE ── */
        .page-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.5px;
            margin-bottom: 0.15rem;
        }
        .page-subtitle { font-size: 0.875rem; color: var(--text-muted); }

        /* Quick action tiles */
        .quick-action-tile {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.1rem 0.75rem;
            background: var(--bg-card2);
            border: 1px solid var(--border);
            border-radius: 16px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.82rem;
            font-weight: 600;
            gap: 0.5rem;
            text-align: center;
            transition: all var(--transition-base);
        }
        .quick-action-tile i {
            font-size: 1.5rem;
            color: var(--primary-light);
        }
        .quick-action-tile:hover {
            background: var(--primary-subtle);
            border-color: var(--primary);
            color: var(--text);
            transform: translateY(-3px);
            box-shadow: var(--shadow-sm);
        }

        /* ── 21. MISC ── */
        .text-muted    { color: var(--text-muted) !important; }  /* ↑ uses brighter variable now */
        .text-dim      { color: var(--text-dim, var(--text-muted)) !important; }
        .text-primary  { color: var(--primary-light) !important; }
        .text-success  { color: var(--success) !important; }
        .text-danger   { color: var(--danger) !important; }
        .text-warning  { color: var(--warning) !important; }
        .text-info     { color: var(--info) !important; }
        .bg-light      { background: var(--bg-card2) !important; }
        .bg-white      { background: var(--bg-card) !important; color: var(--text) !important; }
        .border-bottom { border-color: var(--border) !important; }
        .border-top    { border-color: var(--border) !important; }
        .border        { border-color: var(--border) !important; }
        hr             { border-color: var(--border); opacity: 1; margin: 1.25rem 0; }
        small          { color: var(--text-muted); }
        strong         { color: var(--text); font-weight: 700; }

        /* ── Dropdown theming ── */
        .dropdown-menu {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow-md);
            padding: 0.5rem;
            min-width: 180px;
        }
        .dropdown-item {
            color: var(--text);
            border-radius: 10px;
            font-size: 0.875rem;
            padding: 0.5rem 0.85rem;
            transition: background var(--transition-fast), color var(--transition-fast);
        }
        .dropdown-item:hover, .dropdown-item:focus {
            background: var(--row-hover);
            color: var(--text);
        }
        .dropdown-item.text-danger { color: var(--danger) !important; }
        .dropdown-item.text-danger:hover { background: rgba(239,68,68,0.1); color: var(--danger) !important; }
        .dropdown-divider { border-color: var(--border); margin: 0.35rem 0; }

        /* Buttons */
        .btn {
            border-radius: 12px;
            font-weight: 600;
            transition: transform var(--transition-fast), box-shadow var(--transition-fast), background var(--transition-fast), border-color var(--transition-fast), color var(--transition-fast);
        }
        .btn:hover {
            transform: translateY(-1px);
        }
        .btn-primary {
            box-shadow: 0 8px 20px rgba(218,54,255,0.18);
        }
        .btn-outline-primary,
        .btn-outline-secondary,
        .btn-outline-info,
        .btn-outline-warning,
        .btn-outline-danger {
            border-width: 1.5px;
        }
        .table .btn-group .btn,
        .btn-group.table-actions .btn {
            min-width: 2.35rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Pagination ── */
        .pagination {
            gap: 0.25rem;
            align-items: center;
        }
        .pagination .page-link {
            background: var(--bg-card2);
            border-color: var(--border);
            color: var(--text-muted);
            border-radius: 10px !important;
            margin: 0;
            min-width: 2.5rem;
            text-align: center;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all var(--transition-fast);
        }
        .pagination .page-link:hover {
            background: var(--primary-subtle);
            border-color: var(--primary);
            color: var(--primary-light);
        }
        .pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 2px 10px var(--primary-glow);
        }
        .pagination .page-item.disabled .page-link {
            background: transparent;
            color: var(--text-dim, var(--text-muted));
            opacity: 0.5;
        }

        /* Summary chips */
        .summary-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid;
        }
        .chip-primary { background: var(--primary-subtle); border-color: var(--border-glass); color: var(--primary-light); }
        .chip-warning { background: rgba(245,158,11,0.12); border-color: rgba(245,158,11,0.3); color: #fbbf24; }
        .chip-danger  { background: rgba(239,68,68,0.12);  border-color: rgba(239,68,68,0.3);  color: #f87171; }
        .chip-success { background: rgba(16,185,129,0.12); border-color: rgba(16,185,129,0.3);  color: #34d399; }
        .chip-info    { background: rgba(6,182,212,0.12);  border-color: rgba(6,182,212,0.3);   color: #22d3ee; }

        /* ── 22. FOOTER ── */
        .footer {
            background: var(--bg-card);
            border-top: 1px solid var(--border);
            color: var(--text-muted);
            padding: 0.85rem 0;
            text-align: center;
            font-size: 0.8rem;
            margin-top: auto;
            margin-left: var(--sidebar-w);
            position: relative;
            z-index: 1;
            transition: background var(--transition-slow), border-color var(--transition-base), margin-left 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        body:not(.has-sidebar) .footer { margin-left: 0; }
        body.sidebar-collapsed .footer { margin-left: var(--sidebar-collapsed-w); }

        /* ── 23. RESPONSIVE ── */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                max-width: 100%;
                padding: 1.25rem 1.25rem 4rem;
            }
            .footer { margin-left: 0; }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                top: 60px;
                z-index: 1040;
                box-shadow: 4px 0 30px rgba(0,0,0,0.4);
            }
            .sidebar.show { transform: translateX(0); }
            .main-content { padding: 1rem 1rem 3.5rem; }
            .page-hero { padding: 1.25rem; }
            .page-hero-title { font-size: 1.2rem; }
        }

        /* Sidebar overlay on mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1030;
            backdrop-filter: blur(3px);
        }
        .sidebar-overlay.show { display: block; }

        /* ═══════════════════════════════════════════════════════════
           24. STAT CARDS  — used by all 3 dashboards
        ═══════════════════════════════════════════════════════════ */
        .stat-card {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            padding: 1.4rem 1.3rem;
            color: #fff;
            box-shadow: 0 6px 28px rgba(0,0,0,0.35);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            min-height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 45px rgba(0,0,0,0.42);
        }
        /* Colour variants */
        .stat-purple { background: linear-gradient(135deg, #7c3aed, var(--primary)); }
        .stat-rose   { background: linear-gradient(135deg, var(--secondary), #be185d); }
        .stat-cyan   { background: linear-gradient(135deg, #0ea5e9, #06b6d4); }
        .stat-amber  { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-green  { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-lime   { background: linear-gradient(135deg, #84cc16, #65a30d); }
        .stat-violet { background: linear-gradient(135deg, var(--accent-violet), #7c3aed); }
        .stat-pink   { background: linear-gradient(135deg, #f43f8e, var(--accent-pink)); }
        .stat-indigo { background: linear-gradient(135deg, #6366f1, #4f46e5); }

        /* Decorative glare */
        .stat-card::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 130px; height: 130px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            bottom: -30px; left: -20px;
            width: 90px; height: 90px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            pointer-events: none;
        }

        /* Icon */
        .stat-icon {
            position: absolute;
            top: 1.1rem; right: 1.1rem;
            font-size: 1.6rem;
            opacity: 0.3;
        }

        /* Text elements */
        .stat-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            opacity: 0.85;
            margin-bottom: 0.2rem;
        }
        .stat-number {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 2.4rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.45rem;
            letter-spacing: -1px;
        }
        .stat-trend {
            font-size: 0.78rem;
            opacity: 0.85;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .stat-trend.up     { color: #a7f3d0; }
        .stat-trend.danger { color: #fca5a5; }

        /* ═══════════════════════════════════════════════════════════
           25. PAGE HERO  — masthead banner at top of dashboard pages
        ═══════════════════════════════════════════════════════════ */
        .page-hero {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            padding: 1.75rem 2rem;
            margin-bottom: 1.75rem;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 55%, var(--accent-pink) 100%);
            box-shadow: 0 8px 40px var(--primary-glow), 0 2px 0 rgba(255,255,255,0.07) inset;
            transition: box-shadow 0.3s ease;
        }
        /* Orb decorations */
        .page-hero::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 220px; height: 220px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
            pointer-events: none;
        }
        .page-hero::after {
            content: '';
            position: absolute;
            bottom: -50px; left: 35%;
            width: 160px; height: 160px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
            pointer-events: none;
        }
        .page-hero-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.4rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
            text-shadow: 0 1px 8px rgba(0,0,0,0.2);
            margin: 0;
        }
        .page-hero-subtitle {
            font-size: 0.875rem;
            color: rgba(255,255,255,0.75);
            margin: 0.3rem 0 0;
        }

        /* CTA button inside hero */
        .btn-hero-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.18);
            border: 1.5px solid rgba(255,255,255,0.35);
            color: #fff;
            font-weight: 700;
            font-size: 0.875rem;
            padding: 0.55rem 1.2rem;
            border-radius: 12px;
            text-decoration: none;
            backdrop-filter: blur(6px);
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .btn-hero-primary:hover {
            background: rgba(255,255,255,0.30);
            border-color: rgba(255,255,255,0.6);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }

        /* ═══════════════════════════════════════════════════════════
           26. COUNT-UP ANIMATION  — triggered by IntersectionObserver
        ═══════════════════════════════════════════════════════════ */
        .btn-hero-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(10, 8, 18, 0.16);
            border: 1.5px solid rgba(255,255,255,0.22);
            color: rgba(255,255,255,0.9);
            font-weight: 700;
            font-size: 0.875rem;
            padding: 0.55rem 1.2rem;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-hero-secondary:hover {
            color: #fff;
            border-color: rgba(255,255,255,0.45);
            background: rgba(255,255,255,0.12);
            transform: translateY(-2px);
        }
        .workspace-stack {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .workspace-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .workspace-toolbar-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .workspace-panel {
            background: linear-gradient(180deg, rgba(255,255,255,0.02) 0%, rgba(255,255,255,0) 100%), var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 22px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        .workspace-panel-header {
            padding: 1.15rem 1.35rem 0;
        }
        .workspace-panel-title {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1rem;
            font-weight: 800;
            color: var(--text);
        }
        .workspace-panel-title i { color: var(--primary-light); }
        .workspace-panel-subtitle {
            margin: 0.35rem 0 0;
            color: var(--text-muted);
            font-size: 0.87rem;
        }
        .workspace-panel-body {
            padding: 1.35rem;
        }
        .workspace-filter-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 1rem;
        }
        .workspace-filter-grid > div { grid-column: span 3; }
        .workspace-filter-grid .span-2 { grid-column: span 2; }
        .workspace-filter-grid .span-4 { grid-column: span 4; }
        .workspace-filter-grid .span-5 { grid-column: span 5; }
        .workspace-filter-grid .span-6 { grid-column: span 6; }
        .workspace-filter-grid .span-8 { grid-column: span 8; }
        .workspace-filter-actions {
            display: flex;
            align-items: end;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }
        .metric-card {
            position: relative;
            overflow: hidden;
            min-height: 148px;
            border-radius: 22px;
            padding: 1.2rem 1.15rem;
            color: #fff;
            box-shadow: 0 20px 40px rgba(8, 4, 15, 0.22);
        }
        .metric-card::before {
            content: '';
            position: absolute;
            top: -52px;
            right: -28px;
            width: 128px;
            height: 128px;
            border-radius: 50%;
            background: rgba(255,255,255,0.10);
        }
        .metric-card::after {
            content: '';
            position: absolute;
            left: -18px;
            bottom: -42px;
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
        }
        .metric-card-icon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.55rem;
            opacity: 0.35;
        }
        .metric-card-label {
            position: relative;
            z-index: 1;
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            opacity: 0.88;
        }
        .metric-card-value {
            position: relative;
            z-index: 1;
            margin-top: 1.25rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.04em;
        }
        .metric-card-note {
            position: relative;
            z-index: 1;
            margin-top: 0.6rem;
            font-size: 0.82rem;
            opacity: 0.86;
        }
        .metric-card-primary { background: linear-gradient(135deg, #8b5cf6 0%, #da36ff 100%); }
        .metric-card-cyan { background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%); }
        .metric-card-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .metric-card-amber { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); }
        .metric-card-rose { background: linear-gradient(135deg, #f43f5e 0%, #fb7185 100%); }
        .metric-card-indigo { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
        .info-strip {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .info-pill-card {
            flex: 1;
            min-width: 100px;
            padding: 0.75rem;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--bg-card2);
            text-align: center;
        }
        .info-pill-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }
        .info-pill-value {
            margin-top: 0.4rem;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
        }
        .split-panels {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.5rem;
        }
        .modern-table-wrap {
            overflow-x: auto;
        }
        .modern-table {
            width: 100%;
            min-width: 720px;
            border-collapse: separate;
            border-spacing: 0;
        }
        .modern-table thead th {
            position: sticky;
            top: 0;
            background: var(--bg-card2);
            color: var(--text-muted);
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border-bottom: 1px solid var(--border);
            padding: 0.95rem 1rem;
            z-index: 1;
        }
        .modern-table tbody td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }
        .modern-table tbody tr:nth-child(even) { background: var(--row-alt); }
        .modern-table tbody tr:hover { background: var(--row-hover); }
        .modern-table tbody tr:last-child td { border-bottom: none; }
        .table-title {
            font-weight: 700;
            color: var(--text);
        }
        .table-subtitle {
            margin-top: 0.2rem;
            color: var(--text-muted);
            font-size: 0.82rem;
        }
        .table-meta-stack {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .table-actions {
            display: inline-flex;
            gap: 0.45rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .section-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
        }
        .empty-state-panel {
            padding: 3rem 1.5rem;
            text-align: center;
        }
        .empty-state-panel i {
            font-size: 3rem;
            color: var(--text-dim);
        }
        .empty-state-panel h3,
        .empty-state-panel h5 {
            margin-top: 1rem;
            color: var(--text);
        }
        .empty-state-panel p {
            max-width: 520px;
            margin: 0.65rem auto 0;
            color: var(--text-muted);
        }
        .selection-box {
            max-height: 240px;
            overflow-y: auto;
            padding: 0.35rem;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: var(--bg-card2);
        }
        .selection-option {
            display: flex;
            gap: 0.8rem;
            align-items: start;
            padding: 0.85rem 0.9rem;
            border-radius: 14px;
            transition: background var(--transition-fast);
        }
        .selection-option:hover { background: var(--row-hover); }
        .selection-option .form-check-input { margin-top: 0.2rem; }
        .selection-option-title {
            font-weight: 600;
            color: var(--text);
        }
        .selection-option-note {
            margin-top: 0.2rem;
            font-size: 0.82rem;
            color: var(--text-muted);
        }
        .insight-card {
            border-radius: 20px;
            border: 1px solid var(--border);
            background: var(--bg-card2);
            padding: 1.15rem;
        }
        .insight-card h6 {
            margin-bottom: 0.75rem;
            font-weight: 800;
            color: var(--text);
        }
        .insight-list {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }
        .insight-list-item {
            display: flex;
            gap: 0.75rem;
            align-items: start;
            color: var(--text-muted);
            font-size: 0.86rem;
            line-height: 1.55;
        }
        .insight-list-item i {
            color: var(--primary-light);
            margin-top: 0.1rem;
        }
        @media (max-width: 1200px) {
            .metric-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .workspace-filter-grid > div,
            .workspace-filter-grid .span-2,
            .workspace-filter-grid .span-4,
            .workspace-filter-grid .span-5,
            .workspace-filter-grid .span-6,
            .workspace-filter-grid .span-8 {
                grid-column: span 6;
            }
        }
        @media (max-width: 992px) {
            .split-panels,
            .metric-grid {
                grid-template-columns: 1fr;
            }
            .info-pill-card {
                min-width: 80px;
                padding: 0.5rem;
            }
        }
        @media (max-width: 768px) {
            .workspace-filter-grid > div,
            .workspace-filter-grid .span-2,
            .workspace-filter-grid .span-4,
            .workspace-filter-grid .span-5,
            .workspace-filter-grid .span-6,
            .workspace-filter-grid .span-8 {
                grid-column: span 12;
            }
            .workspace-panel-body,
            .workspace-panel-header {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        @keyframes countPulse {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.08); }
            100% { transform: scale(1); }
        }
        .stat-number.counted { animation: countPulse 0.35s ease; }
    </style>
</head>
<body class="{{ auth()->check() ? 'has-sidebar' : '' }}">
    @include('includes.navigation')

    @yield('content')

    <footer class="footer">
        <span>© {{ date('Y') }} ReproCare — Maternal &amp; Reproductive Health Management System</span>
    </footer>

    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="modal fade app-confirm-modal" id="appConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="app-confirm-icon">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                    <p class="mb-0" id="appConfirmMessage">Are you sure you want to continue?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="appConfirmAccept">Continue</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        /* ============================================================
           REPROCARE GLOBAL JS — Theme + Animations + Utilities
           ============================================================ */

        const RC_THEME_KEY = 'rc_theme';

        /* -- Theme System -- */
        function setTheme(mode) {
            document.documentElement.setAttribute('data-theme', mode);
            localStorage.setItem(RC_THEME_KEY, mode);
            // Sync all toggle switches on page
            document.querySelectorAll('.theme-switch-input').forEach(el => {
                el.checked = (mode === 'dark');
            });
            // Update theme option cards if present
            updateThemeOptionCards(mode);
        }

        function updateThemeOptionCards(mode) {
            document.querySelectorAll('.theme-option').forEach(el => {
                el.classList.toggle('active', el.dataset.theme === mode);
            });
            const checkLight = document.getElementById('check-light');
            const checkDark  = document.getElementById('check-dark');
            if (checkLight) checkLight.style.display = mode === 'light' ? '' : 'none';
            if (checkDark)  checkDark.style.display  = mode === 'dark'  ? '' : 'none';
        }

        /* -- Count-Up Animation -- */
        function animateCountUp(el, target, duration) {
            duration = duration || 1200;
            const start = 0;
            const step  = target / (duration / 16);
            let current = start;
            const timer = setInterval(function() {
                current = Math.min(current + step, target);
                el.textContent = Math.floor(current).toLocaleString();
                if (current >= target) clearInterval(timer);
            }, 16);
        }

        /* -- Sidebar Toggle -- */
        function toggleSidebar() {
            const sidebar  = document.getElementById('sidebar');
            const overlay  = document.getElementById('sidebarOverlay');
            if (!sidebar) return;
            if (window.innerWidth <= 992) {
                sidebar.classList.toggle('show');
                if (overlay) overlay.classList.toggle('show');
                return;
            }

            document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem(
                'rc_sidebar_collapsed',
                document.body.classList.contains('sidebar-collapsed') ? '1' : '0'
            );
        }

        function closeSidebar() {
            const sidebar  = document.getElementById('sidebar');
            const overlay  = document.getElementById('sidebarOverlay');
            if (sidebar)  sidebar.classList.remove('show');
            if (overlay)  overlay.classList.remove('show');
        }

        function extractConfirmMessage(attributeValue) {
            if (!attributeValue || !attributeValue.includes('confirm(')) {
                return null;
            }

            const match = attributeValue.match(/confirm\((['"`])([\s\S]*?)\1\)/);
            return match ? match[2] : null;
        }

        function showAppConfirm(message, onConfirm) {
            const modalEl = document.getElementById('appConfirmModal');
            const messageEl = document.getElementById('appConfirmMessage');
            const acceptButton = document.getElementById('appConfirmAccept');

            if (!modalEl || !messageEl || !acceptButton || typeof bootstrap === 'undefined') {
                return window.confirm(message);
            }

            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            messageEl.textContent = message || 'Are you sure you want to continue?';

            const handleConfirm = function() {
                acceptButton.removeEventListener('click', handleConfirm);
                modal.hide();
                onConfirm();
            };

            acceptButton.addEventListener('click', handleConfirm, { once: true });
            modal.show();
            return false;
        }

        function enhanceLegacyConfirms() {
            document.querySelectorAll('form[onsubmit*="confirm("]').forEach(function(form) {
                const message = extractConfirmMessage(form.getAttribute('onsubmit'));
                if (!message || form.dataset.confirmEnhanced === '1') {
                    return;
                }

                form.dataset.confirmEnhanced = '1';
                form.removeAttribute('onsubmit');

                form.addEventListener('submit', function(event) {
                    if (form.dataset.confirmBypass === '1') {
                        form.dataset.confirmBypass = '0';
                        return;
                    }

                    event.preventDefault();
                    showAppConfirm(message, function() {
                        form.dataset.confirmBypass = '1';
                        form.requestSubmit();
                    });
                });
            });

            document.querySelectorAll('button[onclick*="confirm("], a[onclick*="confirm("]').forEach(function(trigger) {
                const message = extractConfirmMessage(trigger.getAttribute('onclick'));
                if (!message || trigger.dataset.confirmEnhanced === '1') {
                    return;
                }

                trigger.dataset.confirmEnhanced = '1';
                trigger.removeAttribute('onclick');

                trigger.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    showAppConfirm(message, function() {
                        const form = trigger.closest('form');
                        if (form) {
                            form.dataset.confirmBypass = '1';
                            if (typeof form.requestSubmit === 'function') {
                                form.requestSubmit(trigger);
                            } else {
                                form.submit();
                            }
                            return;
                        }

                        if (trigger.tagName === 'A' && trigger.href) {
                            window.location.href = trigger.href;
                        }
                    });
                });
            });
        }

        /* -- Init on DOM ready -- */
        document.addEventListener('DOMContentLoaded', function() {

            /* Apply saved theme */
            const savedTheme = localStorage.getItem(RC_THEME_KEY) || 'light';
            setTheme(savedTheme);

            const sidebarCollapsed = localStorage.getItem('rc_sidebar_collapsed') === '1';
            if (window.innerWidth > 992 && sidebarCollapsed) {
                document.body.classList.add('sidebar-collapsed');
            }

            /* Count-up for all [data-count] elements */
            document.querySelectorAll('[data-count]').forEach(function(el) {
                const target = parseInt(el.getAttribute('data-count'), 10);
                if (!isNaN(target)) animateCountUp(el, target);
            });

            /* Fade-in via IntersectionObserver */
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(e) {
                    if (e.isIntersecting) {
                        e.target.classList.add('visible');
                        observer.unobserve(e.target);
                    }
                });
            }, { threshold: 0.08 });

            document.querySelectorAll('.fade-in-card').forEach(function(el) {
                observer.observe(el);
            });

            /* Sync theme toggle checkboxes */
            document.querySelectorAll('.theme-switch-input').forEach(function(el) {
                el.addEventListener('change', function() {
                    setTheme(this.checked ? 'dark' : 'light');
                });
            });

            /* Theme option cards click */
            document.querySelectorAll('.theme-option').forEach(function(el) {
                el.addEventListener('click', function() {
                    setTheme(this.dataset.theme);
                });
            });

            /* Sidebar toggler */
            const toggler = document.getElementById('sidebarToggleBtn');
            if (toggler) toggler.addEventListener('click', toggleSidebar);

            enhanceLegacyConfirms();

            window.addEventListener('resize', function() {
                if (window.innerWidth <= 992) {
                    closeSidebar();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
