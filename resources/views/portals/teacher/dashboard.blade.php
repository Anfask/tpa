@extends('layouts.app')

@section('title', 'Teacher Dashboard - TPA')
@section('header_title', 'My Dashboard')
@section('header_subtitle', 'Personal performance overview and score tracking')

@section('content')
    {{-- Profile Hero --}}
    <div class="welcome-hero animate-fade-in">
        <div style="position: absolute; top: -50px; right: -50px; width: 180px; height: 180px; border-radius: 50%; background: radial-gradient(circle, rgba(99,102,241,0.25) 0%, transparent 70%); pointer-events: none; z-index: 0;"></div>

        <div class="hero-info">
            <div class="hero-badge">
                <i class="fa-solid fa-circle" style="font-size: 0.45rem; animation: pulse 2s infinite; color: var(--primary);"></i>
                Teacher Portal
            </div>
            <h2 class="hero-name">Welcome back, {{ auth()->user()->name }}!</h2>
            <div class="hero-meta">
                <span><i class="fa-regular fa-envelope"></i> {{ auth()->user()->email }}</span>
                <span class="hero-sep">|</span>
                <span><i class="fa-solid fa-school"></i> {{ auth()->user()->campus->name ?? 'No Campus Assigned' }}</span>
            </div>
        </div>

        <div class="hero-cta">
            <a href="{{ route('teacher.scores') }}" class="btn btn-primary">
                <i class="fa-solid fa-chart-bar"></i> Full Score History
            </a>
        </div>
    </div>

    {{-- KPI Row --}}
    <div class="grid-cols-3">
        <div class="card metric-card card-indigo animate-fade-in delay-100">
            <div class="metric-card-content">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;width:100%;">
                    <div class="metric-info">
                        <span class="metric-label">Weekly Score</span>
                        <span class="metric-value">{{ $weeklyScore !== null ? $weeklyScore . '%' : 'N/A' }}</span>
                    </div>
                    <div class="metric-icon-box"><i class="fa-solid fa-calendar-week"></i></div>
                </div>
                <div class="metric-progress-container" style="margin-top:0.5rem;">
                    <div class="metric-progress-fill" style="width:{{ $weeklyScore !== null ? $weeklyScore : 0 }}%;"></div>
                </div>
                <span class="metric-sub">Current week average</span>
            </div>
        </div>

        <div class="card metric-card card-cyan animate-fade-in delay-200">
            <div class="metric-card-content">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;width:100%;">
                    <div class="metric-info">
                        <span class="metric-label">Monthly Score</span>
                        <span class="metric-value">{{ $monthlyScore !== null ? $monthlyScore . '%' : 'N/A' }}</span>
                    </div>
                    <div class="metric-icon-box"><i class="fa-solid fa-calendar-days"></i></div>
                </div>
                <div class="metric-progress-container" style="margin-top:0.5rem;">
                    <div class="metric-progress-fill" style="width:{{ $monthlyScore !== null ? $monthlyScore : 0 }}%;"></div>
                </div>
                <span class="metric-sub">Current month average</span>
            </div>
        </div>

        <div class="card metric-card card-emerald animate-fade-in delay-300">
            <div class="metric-card-content">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;width:100%;">
                    <div class="metric-info">
                        <span class="metric-label">Overall Rating</span>
                        <span class="metric-value">{{ $overallAvg > 0 ? $overallAvg . '%' : 'N/A' }}</span>
                    </div>
                    <div class="metric-icon-box"><i class="fa-solid fa-star"></i></div>
                </div>
                <div class="metric-progress-container" style="margin-top:0.5rem;">
                    <div class="metric-progress-fill" style="width:{{ $overallAvg > 0 ? $overallAvg : 0 }}%;"></div>
                </div>
                <span class="metric-sub">Lifetime average</span>
            </div>
        </div>
    </div>

    {{-- Chart + Recent Evaluations --}}
    <div class="dashboard-bottom-grid animate-fade-in delay-200">

        {{-- Performance Trend Chart --}}
        <div class="card chart-card">
            <div class="section-header">
                <div class="section-icon" style="--icon-bg: var(--primary-glow); --icon-color: var(--primary);">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div>
                    <h3 class="section-title">Performance Index Trend</h3>
                    <p class="section-subtitle">Score movement over the last 4 months</p>
                </div>
            </div>
            <div class="chart-container chart-area">
                <canvas id="trendChart"></canvas>
            </div>
            {{-- Mobile score summary pills --}}
            <div class="chart-pill-row">
                @foreach(array_combine($months, $scores) as $m => $s)
                    <div class="chart-pill">
                        <span class="chart-pill-month">{{ $m }}</span>
                        <span class="chart-pill-score {{ $s >= 85 ? 'good' : ($s >= 70 ? 'avg' : 'low') }}">
                            {{ $s !== null ? $s . '%' : '—' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Evaluations Log --}}
        <div class="card activity-log-card">
            <div class="section-header">
                <div class="section-icon" style="--icon-bg: var(--secondary-glow); --icon-color: var(--secondary);">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <div>
                    <h3 class="section-title">Recent Activities</h3>
                    <p class="section-subtitle">Latest evaluation records</p>
                </div>
            </div>

            <div class="activity-list">
                @forelse($recentInspections as $ins)
                    @php $scoreClass = $ins->score >= 85 ? 'badge-success' : ($ins->score >= 70 ? 'badge-warning' : 'badge-danger'); @endphp
                    <div class="activity-item">
                        {{-- Score circle --}}
                        <div class="activity-score-ring {{ $ins->score >= 85 ? 'ring-good' : ($ins->score >= 70 ? 'ring-avg' : 'ring-low') }}">
                            {{ $ins->score }}
                        </div>
                        {{-- Details --}}
                        <div class="activity-details">
                            <div class="activity-title">{{ $ins->campusClass->name ?? 'General Class' }}</div>
                            <div class="activity-meta">
                                <span><i class="fa-regular fa-user"></i> {{ $ins->inspector->name ?? 'Admin' }}</span>
                                <span class="dot">·</span>
                                <span><i class="fa-regular fa-clock"></i> {{ $ins->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        {{-- Badge --}}
                        <span class="badge {{ $scoreClass }} activity-badge">{{ $ins->score }}%</span>
                    </div>
                @empty
                    <div class="activity-empty">
                        <i class="fa-regular fa-folder-open"></i>
                        <p>No evaluations recorded yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const ctx = document.getElementById('trendChart').getContext('2d');
    const isDark = document.body.classList.contains('dark-theme');

    const grad = ctx.createLinearGradient(0, 0, 0, 300);
    grad.addColorStop(0, 'rgba(99,102,241,0.4)');
    grad.addColorStop(0.5, 'rgba(99,102,241,0.12)');
    grad.addColorStop(1, 'rgba(99,102,241,0)');

    const grad2 = ctx.createLinearGradient(0, 0, 0, 300);
    grad2.addColorStop(0, 'rgba(14,165,233,0.25)');
    grad2.addColorStop(1, 'rgba(14,165,233,0)');

    const trendChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Monthly Score (%)',
                data: {!! json_encode($scores) !!},
                borderColor: '#6366f1',
                backgroundColor: grad,
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                hoverBackgroundColor: '#6366f1',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        color: isDark ? '#f8fafc' : '#0f172a',
                        font: { family: 'Outfit', size: 12, weight: 600 },
                        usePointStyle: true,
                        pointStyleWidth: 10,
                        padding: 16
                    }
                },
                tooltip: {
                    backgroundColor: isDark ? 'rgba(13,17,28,0.97)' : 'rgba(255,255,255,0.97)',
                    titleColor: isDark ? '#f8fafc' : '#0f172a',
                    bodyColor: isDark ? '#cbd5e1' : '#475569',
                    borderColor: isDark ? 'rgba(255,255,255,0.09)' : 'rgba(226,232,240,0.9)',
                    borderWidth: 1,
                    padding: 14,
                    boxPadding: 6,
                    usePointStyle: true,
                    cornerRadius: 12,
                    titleFont: { family: 'Outfit', size: 13, weight: 700 },
                    bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                    callbacks: {
                        label: ctx => ` Score: ${ctx.parsed.y}%`
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: isDark ? 'rgba(255,255,255,0.04)' : 'rgba(226,232,240,0.5)', drawBorder: false },
                    border: { display: false },
                    ticks: { color: isDark ? '#94a3b8' : '#64748b', font: { family: 'Plus Jakarta Sans', size: 11 } }
                },
                y: {
                    min: 0, max: 100,
                    grid: { color: isDark ? 'rgba(255,255,255,0.04)' : 'rgba(226,232,240,0.5)', drawBorder: false, borderDash: [4,4] },
                    border: { display: false },
                    ticks: {
                        color: isDark ? '#94a3b8' : '#64748b',
                        font: { family: 'Plus Jakarta Sans', size: 11 },
                        callback: v => v + '%',
                        stepSize: 25,
                        padding: 8
                    }
                }
            }
        }
    });

    // Sync chart colours with theme toggle
    const observer = new MutationObserver(() => {
        const dark = document.body.classList.contains('dark-theme');
        const p = trendChart.options.plugins;
        p.legend.labels.color = dark ? '#f8fafc' : '#0f172a';
        p.tooltip.backgroundColor = dark ? 'rgba(13,17,28,0.97)' : 'rgba(255,255,255,0.97)';
        p.tooltip.titleColor = dark ? '#f8fafc' : '#0f172a';
        p.tooltip.bodyColor = dark ? '#cbd5e1' : '#475569';
        p.tooltip.borderColor = dark ? 'rgba(255,255,255,0.09)' : 'rgba(226,232,240,0.9)';
        trendChart.options.scales.x.ticks.color = dark ? '#94a3b8' : '#64748b';
        trendChart.options.scales.y.ticks.color = dark ? '#94a3b8' : '#64748b';
        trendChart.options.scales.x.grid.color = dark ? 'rgba(255,255,255,0.04)' : 'rgba(226,232,240,0.5)';
        trendChart.options.scales.y.grid.color = dark ? 'rgba(255,255,255,0.04)' : 'rgba(226,232,240,0.5)';
        trendChart.update('none');
    });
    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
</script>
@endsection