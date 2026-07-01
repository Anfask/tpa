@extends('layouts.app')

@section('title', 'Super Admin Dashboard - TPA')
@section('header_title', 'Super Admin Dashboard')
@section('header_subtitle', 'System overview and performance monitoring analytics')

@section('content')
    <style>
        .activity-timeline {
            position: relative;
            padding-left: 0.5rem;
            display: flex;
            flex-direction: column;
        }

        .activity-timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 20px;
            bottom: 20px;
            width: 2px;
            background: var(--border-color);
            z-index: 0;
        }

        .activity-item {
            position: relative;
            display: flex;
            gap: 1.25rem;
            padding: 1rem 0;
            z-index: 1;
        }

        .activity-item:first-child {
            padding-top: 0;
        }

        .activity-item:last-child {
            padding-bottom: 0;
        }

        .activity-badge {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 1.1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            background: var(--bg-card);
        }

        .activity-details {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            width: 100%;
        }
    </style>

    <!-- KPI Row -->
    <div class="grid-cols-4">
        <!-- Campus Count -->
        <div class="card metric-card card-indigo animate-fade-in">
            <div class="metric-card-content">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                    <div class="metric-info">
                        <span class="metric-label">Campuses</span>
                        <span class="metric-value">{{ $kpi['campuses_count'] }}</span>
                    </div>
                    <div class="metric-icon-box">
                        <i class="fa-solid fa-school"></i>
                    </div>
                </div>
                <div class="metric-progress-container" style="margin-top: 0.5rem;">
                    <div class="metric-progress-fill" style="width: {{ min(100, ($kpi['campuses_count'] / 10) * 100) }}%;">
                    </div>
                </div>
                <span class="metric-sub">Registered locations</span>
            </div>
        </div>

        <!-- Active Admins -->
        <div class="card metric-card card-cyan animate-fade-in delay-100">
            <div class="metric-card-content">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                    <div class="metric-info">
                        <span class="metric-label">Active Admins</span>
                        <span class="metric-value">{{ $kpi['admins_count'] }}</span>
                    </div>
                    <div class="metric-icon-box">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                </div>
                <div class="metric-progress-container" style="margin-top: 0.5rem;">
                    <div class="metric-progress-fill" style="width: {{ min(100, ($kpi['admins_count'] / 10) * 100) }}%;">
                    </div>
                </div>
                <span class="metric-sub">Campus managers active</span>
            </div>
        </div>

        <!-- Active Teachers -->
        <div class="card metric-card card-emerald animate-fade-in delay-200">
            <div class="metric-card-content">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                    <div class="metric-info">
                        <span class="metric-label">Teachers</span>
                        <span class="metric-value">{{ $kpi['teachers_count'] }}</span>
                    </div>
                    <div class="metric-icon-box">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                </div>
                <div class="metric-progress-container" style="margin-top: 0.5rem;">
                    <div class="metric-progress-fill" style="width: {{ min(100, ($kpi['teachers_count'] / 50) * 100) }}%;">
                    </div>
                </div>
                <span class="metric-sub">Assigned educators</span>
            </div>
        </div>

        <!-- Total Evaluations -->
        <div class="card metric-card card-amber animate-fade-in delay-300">
            <div class="metric-card-content">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                    <div class="metric-info">
                        <span class="metric-label">Evaluations</span>
                        <span class="metric-value">{{ $kpi['inspections_count'] }}</span>
                    </div>
                    <div class="metric-icon-box">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                </div>
                <div class="metric-progress-container" style="margin-top: 0.5rem;">
                    <div class="metric-progress-fill"
                        style="width: {{ min(100, ($kpi['inspections_count'] / 100) * 100) }}%;"></div>
                </div>
                <span class="metric-sub">Evaluations recorded</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="animate-fade-in delay-100" style="margin-top: 0.5rem;">
        <h3
            style="margin-bottom: 1.25rem; font-size: 1.25rem; display: flex; align-items: center; gap: 0.5rem; font-family: var(--font-heading); font-weight: 600;">
            Quick Actions
        </h3>
        <div class="action-grid">
            <a href="{{ route('super-admin.admin-inspection') }}" class="action-card card-indigo">
                <div class="action-card-icon">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div class="action-card-title">Evaluate Campus Admin</div>
                <div class="action-card-desc">Conduct safety, sanitation, and leadership inspections for campus managers.
                </div>
                <div class="action-card-arrow"><i class="fa-solid fa-arrow-right"></i></div>
            </a>

            <a href="{{ route('super-admin.inspection-config') }}" class="action-card card-cyan">
                <div class="action-card-icon">
                    <i class="fa-solid fa-sliders"></i>
                </div>
                <div class="action-card-title">Configure Inspections</div>
                <div class="action-card-desc">Customize assessment rubrics, checklist criteria, and scoring weights.</div>
                <div class="action-card-arrow"><i class="fa-solid fa-arrow-right"></i></div>
            </a>

            <a href="{{ route('super-admin.reports') }}" class="action-card card-emerald">
                <div class="action-card-icon">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
                <div class="action-card-title">Generate Reports</div>
                <div class="action-card-desc">Export logs, scores, and performance analytics summaries as CSV reports.</div>
                <div class="action-card-arrow"><i class="fa-solid fa-arrow-right"></i></div>
            </a>
        </div>
    </div>

    <!-- Detailed Analytics Row -->
    <div class="dashboard-bottom-grid animate-fade-in delay-200" style="margin-top: 0.5rem;">
        <!-- Chart Card -->
        <div class="card" style="display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3>Performance Index Trend</h3>
                <!-- Chart Filters Tabs (Decorative/Visual UX) -->
                <div
                    style="display: flex; gap: 0.25rem; background: rgba(255, 255, 255, 0.03); border: 1px solid var(--border-color); padding: 0.25rem; border-radius: 10px;">
                    <span
                        style="font-size: 0.75rem; font-weight: 700; color: var(--primary); background: var(--primary-glow); padding: 0.25rem 0.6rem; border-radius: 6px; cursor: pointer;">12M</span>
                    <span
                        style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); padding: 0.25rem 0.6rem; border-radius: 6px; cursor: pointer;"
                        onmouseover="this.style.color='var(--primary)'"
                        onmouseout="this.style.color='var(--text-secondary)'">6M</span>
                    <span
                        style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); padding: 0.25rem 0.6rem; border-radius: 6px; cursor: pointer;"
                        onmouseover="this.style.color='var(--primary)'"
                        onmouseout="this.style.color='var(--text-secondary)'">3M</span>
                </div>
            </div>
            <div class="chart-container" style="flex-grow: 1; min-height: 350px;">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        <!-- Recent Activities Timeline -->
        <div class="card" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <h3>Recent Activities</h3>
            <div style="overflow-y: auto; flex-grow: 1; padding-right: 0.25rem;">
                <div class="activity-timeline">
                    @forelse($activities as $act)
                        <div class="activity-item">
                            <div class="activity-details">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                                    <span
                                        style="font-size: 0.9rem; font-weight: 700; color: var(--text-primary); line-height: 1.2;">
                                        {{ $act['title'] }}
                                    </span>
                                    <span
                                        style="font-size: 0.7rem; color: var(--text-muted); background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); padding: 0.15rem 0.5rem; border-radius: 12px; white-space: nowrap;">{{ $act['time'] }}</span>
                                </div>
                                <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0; line-height: 1.35;">
                                    {{ $act['desc'] }}
                                </p>
                                <span
                                    style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; margin-top: 0.15rem; display: inline-flex; align-items: center; gap: 0.25rem;"><i
                                        class="fa-regular fa-user" style="font-size: 0.65rem;"></i> {{ $act['user'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 4rem 0; color: var(--text-muted); font-size: 0.9rem;">
                            <i class="fa-regular fa-folder-open"
                                style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5; display: block;"></i>
                            No recent events logged.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const isDark = document.body.classList.contains('dark-theme');

        // Create gradients for lines
        const grad1 = ctx.createLinearGradient(0, 0, 0, 400);
        grad1.addColorStop(0, 'rgba(16, 185, 129, 0.35)');
        grad1.addColorStop(1, 'rgba(16, 185, 129, 0.01)');

        const grad2 = ctx.createLinearGradient(0, 0, 0, 400);
        grad2.addColorStop(0, 'rgba(99, 102, 241, 0.35)');
        grad2.addColorStop(1, 'rgba(99, 102, 241, 0.01)');

        const grad3 = ctx.createLinearGradient(0, 0, 0, 400);
        grad3.addColorStop(0, 'rgba(6, 182, 212, 0.35)');
        grad3.addColorStop(1, 'rgba(6, 182, 212, 0.01)');

        const performanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [
                    {
                        label: 'Teachers Average (%)',
                        data: {!! json_encode($teacher_trends) !!},
                        borderColor: '#10b981',
                        backgroundColor: grad1,
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false
                    },
                    {
                        label: 'Admins Average (%)',
                        data: {!! json_encode($admin_trends) !!},
                        borderColor: '#6366f1',
                        backgroundColor: grad2,
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false
                    },
                    {
                        label: 'Campuses Average (%)',
                        data: {!! json_encode($campus_trends) !!},
                        borderColor: '#06b6d4',
                        backgroundColor: grad3,
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: isDark ? '#f8fafc' : '#0f172a',
                            font: { family: 'Outfit', size: 13, weight: 500 },
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: isDark ? 'rgba(15, 23, 42, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                        titleColor: isDark ? '#f8fafc' : '#0f172a',
                        bodyColor: isDark ? '#cbd5e1' : '#475569',
                        borderColor: isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(226, 232, 240, 0.8)',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                        titleFont: { family: 'Outfit', size: 14, weight: 600 },
                        bodyFont: { family: 'Plus Jakarta Sans', size: 13 }
                    }
                },
                scales: {
                    x: {
                        grid: { color: isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(226, 232, 240, 0.6)', drawBorder: false },
                        ticks: { color: isDark ? '#94a3b8' : '#64748b', font: { family: 'Plus Jakarta Sans' } }
                    },
                    y: {
                        min: 0,
                        max: 100,
                        grid: { color: isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(226, 232, 240, 0.6)', drawBorder: false, borderDash: [5, 5] },
                        ticks: {
                            color: isDark ? '#94a3b8' : '#64748b',
                            font: { family: 'Plus Jakarta Sans' },
                            callback: function (value) { return value + '%'; },
                            stepSize: 20
                        }
                    }
                }
            }
        });

        // Handle chart theme colors updates
        const observer = new MutationObserver(() => {
            const dark = document.body.classList.contains('dark-theme');
            performanceChart.options.plugins.legend.labels.color = dark ? '#f8fafc' : '#0f172a';
            performanceChart.options.plugins.tooltip.backgroundColor = dark ? 'rgba(15, 23, 42, 0.95)' : 'rgba(255, 255, 255, 0.95)';
            performanceChart.options.plugins.tooltip.titleColor = dark ? '#f8fafc' : '#0f172a';
            performanceChart.options.plugins.tooltip.bodyColor = dark ? '#cbd5e1' : '#475569';
            performanceChart.options.plugins.tooltip.borderColor = dark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(226, 232, 240, 0.8)';
            performanceChart.options.scales.x.ticks.color = dark ? '#94a3b8' : '#64748b';
            performanceChart.options.scales.y.ticks.color = dark ? '#94a3b8' : '#64748b';
            performanceChart.options.scales.x.grid.color = dark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(226, 232, 240, 0.6)';
            performanceChart.options.scales.y.grid.color = dark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(226, 232, 240, 0.6)';
            performanceChart.update();
        });
        observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
    </script>
@endsection