@extends('layouts.app')

@section('title', 'My Scores - TPA')
@section('header_title', 'Performance Evaluation History')
@section('header_subtitle', 'Comprehensive history of class evaluations and ratings')

@section('content')
    <div style="display: flex; flex-direction: column; gap: 1.5rem;" class="animate-fade-in">
        <!-- Trend & Info Section -->
        <div class="grid-cols-3" style="grid-template-columns: 2fr 1fr; gap: 1.5rem;">
            <!-- Chart -->
            <div class="card" style="display: flex; flex-direction: column;">
                <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    6 Month Performance Trend
                </h3>
                <div class="chart-container" style="flex-grow: 1; min-height: 300px;">
                    <canvas id="scoresTrendChart"></canvas>
                </div>
            </div>

            <!-- Info / Summary -->
            <div class="card" style="display: flex; flex-direction: column; gap: 1.25rem; align-self: stretch;">
                <h3>Evaluation Insights</h3>
                <p style="font-size: 0.85rem; line-height: 1.6; color: var(--text-secondary);">
                    Evaluations are compiled using criteria designed by administrators. Scores reflect overall board work,
                    student engagement, subject competency, and classroom control.
                </p>
                <div
                    style="margin-top: auto; padding: 1rem; border-radius: 16px; background: rgba(var(--primary-glow), 0.05); border: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 0.5rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                        <span style="color: var(--text-secondary);">Total Evaluations:</span>
                        <strong style="color: var(--text-primary);">{{ $inspections->count() }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                        <span style="color: var(--text-secondary);">Average Score:</span>
                        <strong
                            style="color: var(--accent);">{{ $inspections->count() > 0 ? round($inspections->avg('score'), 1) . '%' : 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="card" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <h3>Detailed Evaluation History</h3>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Class Target</th>
                            <th>Evaluator</th>
                            <th>Score (%)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inspections as $ins)
                            <tr>
                                <td>{{ $ins->created_at->format('F j, Y, g:i a') }}</td>
                                <td><strong>{{ $ins->campusClass->name ?? 'General Class' }}</strong></td>
                                <td>{{ $ins->inspector->name ?? 'Administrator' }}</td>
                                <td>
                                    <span class="score-badge"
                                        style="font-size: 1.05rem; padding: 0.2rem 0.6rem; background: {{ $ins->score >= 85 ? 'var(--accent-glow)' : ($ins->score >= 70 ? 'var(--warning-glow)' : 'var(--danger-glow)') }}; color: {{ $ins->score >= 85 ? 'var(--accent)' : ($ins->score >= 70 ? 'var(--warning)' : 'var(--danger)') }};">
                                        {{ $ins->score }}%
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('teacher.inspections.view', $ins->id) }}" class="btn btn-secondary"
                                        style="padding: 0.45rem 0.9rem; font-size: 0.82rem; border-radius: 10px; gap: 0.35rem;">
                                        <i class="fa-solid fa-file-invoice"></i> View Breakdown
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                                    <i class="fa-regular fa-folder-open"
                                        style="font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.3; display: block;"></i>
                                    No evaluations logged for your account yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const ctx = document.getElementById('scoresTrendChart').getContext('2d');
        const isDark = document.body.classList.contains('dark-theme');

        const grad = ctx.createLinearGradient(0, 0, 0, 300);
        grad.addColorStop(0, 'rgba(14, 165, 233, 0.35)');
        grad.addColorStop(1, 'rgba(14, 165, 233, 0.01)');

        const scoresTrendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [{
                    label: 'Monthly Rating (%)',
                    data: {!! json_encode($scoreData) !!},
                    borderColor: '#0ea5e9',
                    backgroundColor: grad,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#0ea5e9',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8
                }]
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
                        min: 0, max: 100,
                        grid: { color: isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(226, 232, 240, 0.6)', drawBorder: false, borderDash: [5, 5] },
                        ticks: {
                            color: isDark ? '#94a3b8' : '#64748b',
                            font: { family: 'Plus Jakarta Sans' },
                            callback: v => v + '%',
                            stepSize: 20
                        }
                    }
                }
            }
        });

        const observer = new MutationObserver(() => {
            const dark = document.body.classList.contains('dark-theme');
            scoresTrendChart.options.plugins.legend.labels.color = dark ? '#f8fafc' : '#0f172a';
            scoresTrendChart.options.plugins.tooltip.backgroundColor = dark ? 'rgba(15, 23, 42, 0.95)' : 'rgba(255, 255, 255, 0.95)';
            scoresTrendChart.options.plugins.tooltip.titleColor = dark ? '#f8fafc' : '#0f172a';
            scoresTrendChart.options.plugins.tooltip.bodyColor = dark ? '#cbd5e1' : '#475569';
            scoresTrendChart.options.plugins.tooltip.borderColor = dark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(226, 232, 240, 0.8)';
            scoresTrendChart.options.scales.x.ticks.color = dark ? '#94a3b8' : '#64748b';
            scoresTrendChart.options.scales.y.ticks.color = dark ? '#94a3b8' : '#64748b';
            scoresTrendChart.options.scales.x.grid.color = dark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(226, 232, 240, 0.6)';
            scoresTrendChart.options.scales.y.grid.color = dark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(226, 232, 240, 0.6)';
            scoresTrendChart.update();
        });
        observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
    </script>
@endsection