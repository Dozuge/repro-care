@extends('cho.layout')

@section('title', 'City Health Analytics - CHO Portal | ReproCare')

@section('cho-content')

<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-bar-chart-fill me-2" style="color:var(--primary-light);"></i>City Health Analytics
            </div>
            <p class="page-hero-subtitle">
                City-wide reproductive health surveillance, compliance monitoring, and AI-powered insights.
            </p>
        </div>
        <a href="{{ route('cho.sms.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-chat-dots-fill me-1"></i> SMS Logs
            <span class="badge bg-primary ms-1">{{ $smsEnabledCount }} enabled</span>
        </a>
    </div>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card fade-in-card p-3 d-flex flex-row align-items-center justify-content-between" style="border-radius:16px;">
            <div>
                <div class="text-muted text-xs">Total Active Pregnancies</div>
                <h3 class="fw-800 text-primary mb-0 mt-1">{{ $totalPregnant }}</h3>
            </div>
            <i class="bi bi-heart-pulse-fill text-primary" style="font-size:2rem; opacity:0.8;"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card fade-in-card p-3 d-flex flex-row align-items-center justify-content-between" style="border-radius:16px;">
            <div>
                <div class="text-muted text-xs">High-Risk Pregnancies</div>
                <h3 class="fw-800 text-danger mb-0 mt-1">{{ $highRiskCount }}</h3>
            </div>
            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:2rem; opacity:0.8;"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card fade-in-card p-3 d-flex flex-row align-items-center justify-content-between" style="border-radius:16px;">
            <div>
                <div class="text-muted text-xs">Completed Pregnancies</div>
                <h3 class="fw-800 text-success mb-0 mt-1">{{ $totalCompleted }}</h3>
            </div>
            <i class="bi bi-calendar-check-fill text-success" style="font-size:2rem; opacity:0.8;"></i>
        </div>
    </div>
</div>

{{-- ANC + Facility Delivery --}}
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card fade-in-card h-100">
            <div class="card-header">
                <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    <i class="bi bi-shield-check me-2" style="color:var(--primary-light);"></i>
                    Antenatal Care (ANC) Coverage
                </h5>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                <div class="position-relative mb-3" style="width:140px; height:140px;">
                    <svg viewBox="0 0 36 36" class="w-100 h-100">
                        <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--border)" stroke-width="3" />
                        <path class="circle" stroke-dasharray="{{ $ancCoverageRate }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--primary-light)" stroke-width="3" stroke-linecap="round" />
                    </svg>
                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                        <h4 class="fw-800 mb-0">{{ $ancCoverageRate }}%</h4>
                        <span style="font-size:0.68rem; color:var(--text-muted);">Compliance</span>
                    </div>
                </div>
                <p class="text-center text-xs text-muted max-w-280">
                    Percentage of completed pregnancies meeting DOH standard of at least 4 prenatal checkups.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card fade-in-card h-100">
            <div class="card-header">
                <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    <i class="bi bi-hospital me-2" style="color:#10b981;"></i>
                    Facility-Based Delivery Rate
                </h5>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                <div class="position-relative mb-3" style="width:140px; height:140px;">
                    <svg viewBox="0 0 36 36" class="w-100 h-100">
                        <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--border)" stroke-width="3" />
                        <path class="circle" stroke-dasharray="{{ $facilityBirthRate }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" />
                    </svg>
                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                        <h4 class="fw-800 mb-0" style="color:#10b981;">{{ $facilityBirthRate }}%</h4>
                        <span style="font-size:0.68rem; color:var(--text-muted);">SBA Rate</span>
                    </div>
                </div>
                <p class="text-center text-xs text-muted max-w-280">
                    Percentage of deliveries conducted inside a licensed medical facility or clinic (DOH FBD standard).
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card fade-in-card">
            <div class="card-header">
                <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    <i class="bi bi-graph-up-arrow me-2" style="color:var(--primary-light);"></i>
                    Monthly Pregnancy Enrollments
                </h5>
            </div>
            <div class="card-body">
                <div style="height: 300px; position: relative;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card fade-in-card">
            <div class="card-header">
                <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    <i class="bi bi-journal-x me-2" style="color:var(--danger);"></i>
                    Mortality Factors
                </h5>
            </div>
            <div class="card-body">
                @if(count($causes) > 0)
                    <div style="height: 220px; position: relative;" class="mb-3">
                        <canvas id="mortalityChart"></canvas>
                    </div>
                    <div style="font-size:0.75rem;">
                        <ul class="list-group list-group-flush mb-0">
                            @foreach($causes as $cat => $count)
                                <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-1.5 px-0" style="border:none;">
                                    <span class="text-muted"><i class="bi bi-circle-fill me-2" style="font-size:0.5rem; color: var(--danger);"></i>{{ str_replace('_', ' ', ucfirst($cat)) }}</span>
                                    <span class="fw-700">{{ $count }} case(s)</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-shield-check" style="font-size: 2.5rem; color: var(--success);"></i>
                        <h6 class="mt-2">Zero Maternal Deaths</h6>
                        <p class="text-muted text-xs">No mortality cases are logged in the system.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Priority List + AI INSIGHTS + CHAT ───────────────────────────────────── --}}
<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="card fade-in-card h-100">
            <div class="card-header">
                <h5 class="mb-0 fw-700"><i class="bi bi-list-ul me-2" style="color:var(--primary-light);"></i>Priority Patients</h5>
            </div>
            <div class="card-body">
                @if(isset($prioritized) && $prioritized->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($prioritized as $item)
                            @php $p = $item['patient']; $score = $item['priority_score']; @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-700">{{ $p->name }}</div>
                                    <div class="text-muted small">{{ $p->barangay ?? '—' }} • {{ $p->contact_number ?? 'No contact' }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="badge bg-danger">{{ $score }}</div>
                                    <div class="text-muted small mt-1">{{ optional($p->healthRecords()->latest()->first())->risk_level ?? 'N/A' }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-4 text-muted">No prioritized patients available.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-7">

<div class="row g-4">
    {{-- AI Insights Panel --}}
    <div class="col-lg-7">
        <div class="card fade-in-card" style="border: 1px solid rgba(218,54,255,0.25);">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg,rgba(218,54,255,0.08),rgba(99,102,241,0.08));">
                <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    <i class="bi bi-stars me-2" style="color:#da36ff;"></i>
                    AI-Powered Health Insights
                    <span class="badge ms-2" style="background:rgba(218,54,255,0.15); color:#da36ff; font-size:.65rem; font-weight:600;">Gemini 1.5 Flash</span>
                </h5>
                <button class="btn btn-sm" id="refreshInsightsBtn"
                    style="background:rgba(218,54,255,0.1); color:#da36ff; border:1px solid rgba(218,54,255,0.3);"
                    title="Refresh AI Insights">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
            <div class="card-body" id="aiInsightsBody" style="line-height:1.8;">
                @php
                    $insightLines = array_filter(explode("\n", $aiInsights), fn($l) => trim($l) !== '');
                @endphp
                <div id="aiInsightsContent">
                    @foreach($insightLines as $line)
                        @php $line = trim($line); @endphp
                        @if(preg_match('/^\d+\./', $line))
                            <div class="d-flex gap-2 mb-3 p-3 rounded-3" style="background:rgba(255,255,255,0.03); border-left: 3px solid rgba(218,54,255,0.5);">
                                <span style="font-size:.87rem;">{{ $line }}</span>
                            </div>
                        @elseif(!str_starts_with($line, '---') && !str_starts_with($line, '_💡'))
                            <p class="text-muted mb-1" style="font-size:.85rem;">{{ $line }}</p>
                        @endif
                    @endforeach
                    @if(empty(array_filter($insightLines)))
                        <p class="text-muted text-center py-3">
                            <i class="bi bi-key me-1"></i>Configure <code>GEMINI_API_KEY</code> in <code>.env</code> to enable AI-powered recommendations.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- AI Chat Widget --}}
    <div class="col-lg-5">
        <div class="card fade-in-card" style="border: 1px solid rgba(99,102,241,0.25); min-height: 400px;">
            <div class="card-header" style="background: linear-gradient(135deg,rgba(99,102,241,0.08),rgba(59,130,246,0.08));">
                <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    <i class="bi bi-chat-square-dots-fill me-2" style="color:#6366f1;"></i>
                    Ask the AI Advisor
                </h5>
            </div>
            <div class="card-body d-flex flex-column" style="gap:1rem; height:380px;">
                {{-- Chat history --}}
                <div id="chatHistory" style="flex:1; overflow-y:auto; display:flex; flex-direction:column; gap:.75rem;">
                    <div class="d-flex gap-2 align-items-start">
                        <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#3b82f6);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-stars" style="color:#fff;font-size:.75rem;"></i>
                        </div>
                        <div class="rounded-3 p-2" style="background:rgba(99,102,241,0.1); font-size:.83rem; max-width:90%;">
                            Hello! I'm your AI health advisor. Ask me anything about the city's maternal health data — e.g., <em>"What should we prioritize to reduce maternal deaths?"</em>
                        </div>
                    </div>
                </div>
                {{-- Input area --}}
                <div>
                    <div class="input-group">
                        <input type="text" id="chatInput" class="form-control" style="font-size:.85rem;"
                            placeholder="Ask a health question..." maxlength="500"
                            onkeydown="if(event.key==='Enter'){sendChat();}">
                        <button class="btn btn-primary" id="chatSendBtn" onclick="sendChat()">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                    <div class="text-muted mt-1" style="font-size:.7rem;">
                        <i class="bi bi-shield-lock me-1"></i>Based on your live city health data.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pregnancy Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const trendMonths = {!! json_encode(array_keys($monthlyPregnancies)) !!};
    const trendCounts = {!! json_encode(array_values($monthlyPregnancies)) !!};
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendMonths.length > 0 ? trendMonths : ['No Data'],
            datasets: [{
                label: 'Pregnancies',
                data: trendCounts.length > 0 ? trendCounts : [0],
                borderColor: '#da36ff',
                backgroundColor: 'rgba(218,54,255,0.06)',
                borderWidth: 3, tension: 0.35, fill: true
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Mortality Doughnut Chart
    const mortalityCanvas = document.getElementById('mortalityChart');
    if (mortalityCanvas) {
        const mortalityCtx = mortalityCanvas.getContext('2d');
        new Chart(mortalityCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_map(fn($v) => str_replace('_', ' ', ucfirst($v)), array_keys($causes))) !!},
                datasets: [{
                    data: {!! json_encode(array_values($causes)) !!},
                    backgroundColor: ['#ef4444','#f59e0b','#3b82f6','#10b981','#8b5cf6','#ec4899','#6b7280'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '70%'
            }
        });
    }
});

// ── AI Chat ──────────────────────────────────────────────────────────────────
async function sendChat() {
    const input = document.getElementById('chatInput');
    const question = input.value.trim();
    if (!question) return;

    const chatHistory = document.getElementById('chatHistory');
    const sendBtn = document.getElementById('chatSendBtn');

    chatHistory.innerHTML += `
        <div class="d-flex gap-2 align-items-start flex-row-reverse">
            <div style="width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-person-fill" style="color:#fff;font-size:.75rem;"></i>
            </div>
            <div class="rounded-3 p-2" style="background:rgba(218,54,255,0.12); font-size:.83rem; max-width:85%;">
                ${escapeHtml(question)}
            </div>
        </div>`;

    input.value = '';
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    chatHistory.scrollTop = chatHistory.scrollHeight;

    const thinkingId = 'thinking_' + Date.now();
    chatHistory.innerHTML += `
        <div class="d-flex gap-2 align-items-start" id="${thinkingId}">
            <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#3b82f6);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-stars" style="color:#fff;font-size:.75rem;"></i>
            </div>
            <div class="rounded-3 p-2" style="background:rgba(99,102,241,0.1); font-size:.83rem;">
                <i class="bi bi-three-dots text-muted"></i> Thinking...
            </div>
        </div>`;
    chatHistory.scrollTop = chatHistory.scrollHeight;

    try {
        const response = await fetch('{{ route('cho.analytics.chat') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ question })
        });
        const data = await response.json();
        document.getElementById(thinkingId)?.remove();

        chatHistory.innerHTML += `
            <div class="d-flex gap-2 align-items-start">
                <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#3b82f6);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-stars" style="color:#fff;font-size:.75rem;"></i>
                </div>
                <div class="rounded-3 p-2" style="background:rgba(99,102,241,0.1); font-size:.83rem; max-width:90%; line-height:1.6;">
                    ${escapeHtml(data.answer || 'No response.').replace(/\n/g,'<br>')}
                </div>
            </div>`;
    } catch (e) {
        document.getElementById(thinkingId)?.remove();
        chatHistory.innerHTML += `
            <div class="text-danger p-2 rounded-3" style="font-size:.8rem; background:rgba(239,68,68,0.08);">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>Failed to get a response. Please try again.
            </div>`;
    }

    sendBtn.disabled = false;
    sendBtn.innerHTML = '<i class="bi bi-send-fill"></i>';
    chatHistory.scrollTop = chatHistory.scrollHeight;
}

function escapeHtml(text) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(text));
    return d.innerHTML;
}

document.getElementById('refreshInsightsBtn')?.addEventListener('click', function() {
    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    window.location.reload(true);
});
</script>
@endpush

@endsection
