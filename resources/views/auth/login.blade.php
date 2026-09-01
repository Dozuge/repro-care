@extends('layouts.app')

@section('title', 'Sign In - ReproCare')

@push('scripts')
<script>
    // Clear any session storage on login page
    sessionStorage.clear();

    // Prevent browser autofill
    window.addEventListener('load', function() {
        setTimeout(function() {
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            if (email && !email.value.includes('@')) email.value = '';
            if (password) password.value = '';
        }, 100);
    });

    window.toggleLoginPassword = function() {
        const input = document.getElementById('password');
        const icon = document.getElementById('passwordToggleIcon');

        if (!input || !icon) {
            return;
        }

        const showPassword = input.type === 'password';
        input.type = showPassword ? 'text' : 'password';
        icon.className = showPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
    };
</script>
@endpush

@push('styles')
<style>
    /* ── Auth layout: no sidebar, no padding-top body offset ── */
    body { padding-top: 0 !important; background: var(--bg-main) !important; }
    nav.navbar { display: none !important; }
    footer.footer { display: none !important; }

    .login-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: stretch;
        overflow: hidden;
    }

    /* ── LEFT DECORATIVE PANEL ── */
    .login-left {
        flex: 1;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 3rem;
    }

    [data-theme="dark"] .login-left {
        background: linear-gradient(160deg, #0d0420 0%, #1e0840 45%, #2a0a5a 100%);
    }
    [data-theme="light"] .login-left {
        background: linear-gradient(160deg, #f3e8ff 0%, #e9d5ff 45%, #ddd6fe 100%);
    }

    /* Animated orbs */
    .orb {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }
    .orb-1 {
        width: 480px; height: 480px;
        top: -160px; right: -120px;
        animation: floatOrb1 10s ease-in-out infinite;
    }
    [data-theme="dark"]  .orb-1 { background: radial-gradient(circle, rgba(218,54,255,0.30) 0%, transparent 70%); }
    [data-theme="light"] .orb-1 { background: radial-gradient(circle, rgba(218,54,255,0.18) 0%, transparent 70%); }

    .orb-2 {
        width: 360px; height: 360px;
        bottom: -120px; left: -80px;
        animation: floatOrb2 13s ease-in-out infinite;
    }
    [data-theme="dark"]  .orb-2 { background: radial-gradient(circle, rgba(244,63,142,0.22) 0%, transparent 70%); }
    [data-theme="light"] .orb-2 { background: radial-gradient(circle, rgba(244,63,142,0.15) 0%, transparent 70%); }

    .orb-3 {
        width: 200px; height: 200px;
        top: 50%; left: 40%;
        transform: translate(-50%, -50%);
        animation: floatOrb3 8s ease-in-out infinite;
    }
    [data-theme="dark"]  .orb-3 { background: radial-gradient(circle, rgba(155,54,255,0.18) 0%, transparent 70%); }
    [data-theme="light"] .orb-3 { background: radial-gradient(circle, rgba(155,54,255,0.12) 0%, transparent 70%); }

    @keyframes floatOrb1 {
        0%,100% { transform: translate(0,0) scale(1); }
        33%     { transform: translate(-30px, 30px) scale(1.08); }
        66%     { transform: translate(20px,-20px) scale(0.95); }
    }
    @keyframes floatOrb2 {
        0%,100% { transform: translate(0,0) scale(1); }
        40%     { transform: translate(40px,-25px) scale(1.12); }
        70%     { transform: translate(-15px, 15px) scale(0.92); }
    }
    @keyframes floatOrb3 {
        0%,100% { transform: translate(-50%,-50%) scale(1); }
        50%     { transform: translate(-50%,-55%) scale(1.15); }
    }

    /* Left content */
    .left-content {
        position: relative;
        z-index: 2;
        text-align: center;
        max-width: 400px;
    }

    .brand-logo-hero {
        width: 108px; height: 108px;
        border-radius: 30px;
        background: rgba(255,255,255,0.18);
        display: grid;
        place-items: center;
        padding: 0.75rem;
        margin: 0 auto 1.5rem;
        box-shadow: 0 12px 40px rgba(105, 50, 164, 0.18), 0 0 80px rgba(218,54,255,0.16);
        backdrop-filter: blur(14px);
        animation: heroFloat 3.5s ease-in-out infinite;
    }
    .brand-logo-hero img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 22px;
    }
    @keyframes heroFloat {
        0%,100% { transform: translateY(0) rotate(0deg); }
        50%     { transform: translateY(-10px) rotate(-2deg); }
    }

    [data-theme="dark"]  .left-content h1 { color: #fff; }
    [data-theme="light"] .left-content h1 { color: var(--text); }
    .left-content h1 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 2.6rem;
        font-weight: 800;
        letter-spacing: -1px;
        margin-bottom: 0.5rem;
    }

    [data-theme="dark"]  .left-content p { color: rgba(255,255,255,0.72); }
    [data-theme="light"] .left-content p { color: var(--text-muted); }
    .left-content p {
        font-size: 1rem;
        max-width: 300px;
        margin: 0 auto 2.5rem;
        line-height: 1.6;
    }

    /* Feature list */
    .feature-list { list-style: none; padding: 0; text-align: left; display: inline-block; }
    .feature-list li {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        margin-bottom: 0.85rem;
        font-size: 0.9rem;
        animation: featureFadeIn 0.5s ease both;
    }
    .feature-list li:nth-child(1) { animation-delay: 0.1s; }
    .feature-list li:nth-child(2) { animation-delay: 0.2s; }
    .feature-list li:nth-child(3) { animation-delay: 0.3s; }
    .feature-list li:nth-child(4) { animation-delay: 0.4s; }
    .feature-list li:nth-child(5) { animation-delay: 0.5s; }
    @keyframes featureFadeIn {
        from { opacity: 0; transform: translateX(-12px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    .feature-icon {
        width: 34px; height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
        background: linear-gradient(135deg, var(--primary), var(--accent-violet));
        color: #fff;
        box-shadow: 0 3px 10px var(--primary-glow);
    }

    [data-theme="dark"]  .feature-list li span { color: rgba(255,255,255,0.85); }
    [data-theme="light"] .feature-list li span { color: var(--text); }
    .feature-list li span { font-weight: 500; }

    /* ── RIGHT FORM PANEL ── */
    .login-right {
        width: 480px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 2.5rem;
        overflow-y: auto;
        position: relative;
    }

    [data-theme="dark"]  .login-right {
        background: var(--bg-card);
        border-left: 1px solid var(--border-glass);
    }
    [data-theme="light"] .login-right {
        background: rgba(255,255,255,0.92);
        border-left: 1px solid var(--border);
        backdrop-filter: blur(20px);
    }

    .login-form-wrap { width: 100%; max-width: 380px; }

    .login-form-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 0.3rem;
        color: var(--text);
    }
    .login-form-sub {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-bottom: 2rem;
        line-height: 1.5;
    }

    /* Icon input */
    .field-group { margin-bottom: 1.2rem; }

    .input-icon-wrap {
        position: relative;
    }
    .input-icon-wrap .field-icon {
        position: absolute;
        left: 0.95rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 1rem;
        pointer-events: none;
        z-index: 2;
        transition: color 0.2s ease;
    }
    .input-icon-wrap:focus-within .field-icon { color: var(--primary); }

    .input-icon-wrap input {
        padding-left: 2.65rem;
        padding-right: 3.25rem;
        width: 100%;
        height: 50px;
        background: var(--bg-input);
        border: 1.5px solid var(--border);
        border-radius: 14px;
        color: var(--text);
        font-size: 0.9rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.3s ease;
        outline: none;
    }
    .input-icon-wrap input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
        background: var(--bg-input);
    }
    .input-icon-wrap input::placeholder { color: var(--text-muted); opacity: 0.65; }
    .password-toggle {
        position: absolute;
        right: 0.7rem;
        top: 50%;
        transform: translateY(-50%);
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 10px;
        background: transparent;
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        z-index: 3;
        transition: all 0.18s ease;
    }
    .password-toggle:hover {
        background: var(--primary-subtle);
        color: var(--primary);
    }

    /* Login button */
    .btn-login {
        width: 100%;
        height: 50px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border: none;
        border-radius: 14px;
        color: #fff;
        font-size: 0.95rem;
        font-weight: 700;
        font-family: 'Plus Jakarta Sans', sans-serif;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 6px 24px var(--primary-glow);
        position: relative;
        overflow: hidden;
        margin-top: 0.5rem;
    }
    .btn-login::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transform: translateX(-100%);
        transition: transform 0.5s ease;
    }
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 36px var(--primary-glow);
    }
    .btn-login:hover::after { transform: translateX(100%); }
    .btn-login:active { transform: scale(0.97); }

    /* Divider */
    .auth-divider {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 1.5rem 0;
    }
    .auth-divider::before, .auth-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }
    .auth-divider span { font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; }

    /* Register link */
    .register-link-wrap {
        text-align: center;
        font-size: 0.875rem;
        color: var(--text-muted);
    }
    .register-link-wrap a {
        color: var(--primary-light);
        font-weight: 600;
        text-decoration: none;
        transition: color 0.15s ease;
    }
    .register-link-wrap a:hover { color: var(--primary); }

    @media (max-width: 768px) {
        .login-left { display: none; }
        .login-right { width: 100%; border-left: none; padding: 2.5rem 1.5rem; }
    }
    @media (max-width: 480px) {
        .login-right { padding: 2rem 1.25rem; }
        .login-form-title { font-size: 1.5rem; }
    }
</style>
@endpush

@section('content')
<div class="login-wrapper">

    {{-- ── LEFT PANEL ── --}}
    <div class="login-left">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="left-content">
            <div class="brand-logo-hero">
                <img src="{{ asset('images/brand/reprocare-logo.svg') }}" alt="ReproCare logo">
            </div>
            <h1>ReproCare</h1>
            <p>Your comprehensive maternal &amp; reproductive health companion</p>

            <ul class="feature-list">
                <li>
                    <span class="feature-icon"><i class="bi bi-calendar-heart"></i></span>
                    <span>Period &amp; Cycle Tracking</span>
                </li>
                <li>
                    <span class="feature-icon"><i class="bi bi-heart-pulse"></i></span>
                    <span>Pregnancy Monitoring</span>
                </li>
                <li>
                    <span class="feature-icon"><i class="bi bi-graph-up-arrow"></i></span>
                    <span>Health Analytics &amp; Reports</span>
                </li>
                <li>
                    <span class="feature-icon"><i class="bi bi-chat-dots"></i></span>
                    <span>Community &amp; Learning Hub</span>
                </li>
                <li>
                    <span class="feature-icon"><i class="bi bi-shield-check"></i></span>
                    <span>Secure &amp; Private Records</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- ── RIGHT FORM PANEL ── --}}
    <div class="login-right">
        <div class="login-form-wrap">

            <h2 class="login-form-title">Welcome back! 👋</h2>
            <p class="login-form-sub">Sign in to continue to your health dashboard</p>

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-3 d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-circle-fill mt-1 flex-shrink-0"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('pending_registration'))
                <div class="alert mb-3 d-flex align-items-start gap-2" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.4);border-radius:14px;color:var(--text);">
                    <i class="bi bi-hourglass-split flex-shrink-0 mt-1" style="color:#f59e0b;font-size:1.1rem;"></i>
                    <div>
                        <div style="font-weight:700;font-size:0.9rem;color:#f59e0b;margin-bottom:0.2rem;">Registration Submitted!</div>
                        <div style="font-size:0.82rem;color:var(--text-muted);">
                            Your account is <strong>pending approval</strong> by your Barangay Health Worker (BHW).
                            You will be able to log in once it is approved. Please check back later.
                        </div>
                    </div>
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="field-group">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-envelope field-icon"></i>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="your@email.com"
                               required
                               autofocus
                               autocomplete="off"
                               readonly
                               onfocus="this.removeAttribute('readonly')">
                    </div>
                </div>

                <div class="field-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-lock field-icon"></i>
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="••••••••"
                               required
                               autocomplete="new-password"
                               readonly
                               onfocus="this.removeAttribute('readonly')">
                        <button type="button" class="password-toggle" onclick="toggleLoginPassword()" aria-label="Show or hide password">
                            <i class="bi bi-eye" id="passwordToggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Sign In
                </button>
            </form>

            <div class="auth-divider"><span>or</span></div>

            <div class="register-link-wrap">
                Don't have an account?
                <a href="{{ route('register') }}">Create one free</a>
            </div>

        </div>
    </div>

</div>
@endsection
