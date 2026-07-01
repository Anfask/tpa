@extends('layouts.app')

@section('title', 'Admin Dashboard - TPA')
@section('header_title', 'Campus Dashboard')
@section('header_subtitle', 'Oversight on teaching quality and safety checklists')

@section('content')
    <!-- KPI Row -->
    <div class="grid-cols-4">
        <!-- Teachers Count -->
        <div class="card metric-card card-indigo animate-fade-in">
            <div class="metric-card-content">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                    <div class="metric-info">
                        <span class="metric-label">Teachers</span>
                        <span class="metric-value">{{ $teachersCount }}</span>
                    </div>
                    <div class="metric-icon-box">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                </div>
                <div class="metric-progress-container" style="margin-top: 0.5rem;">
                    <div class="metric-progress-fill" style="width: {{ min(100, ($teachersCount / 15) * 100) }}%;"></div>
                </div>
                <span class="metric-sub">In your campus</span>
            </div>
        </div>

        <!-- Pending Evaluations -->
        <div class="card metric-card card-amber animate-fade-in delay-100">
            <div class="metric-card-content">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                    <div class="metric-info">
                        <span class="metric-label">Pending Evals</span>
                        <span class="metric-value">{{ $pendingInspectionsCount }}</span>
                    </div>
                    <div class="metric-icon-box">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                </div>
                <div class="metric-progress-container" style="margin-top: 0.5rem;">
                    <div class="metric-progress-fill"
                        style="width: {{ max(10, min(100, $pendingInspectionsCount * 20)) }}%;"></div>
                </div>
                <span class="metric-sub">Due this month</span>
            </div>
        </div>

        <!-- Campus Average Score -->
        <div class="card metric-card card-cyan animate-fade-in delay-200">
            <div class="metric-card-content">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                    <div class="metric-info">
                        <span class="metric-label">Campus Score</span>
                        <span class="metric-value">{{ $avgCampusScore }}%</span>
                    </div>
                    <div class="metric-icon-box">
                        <i class="fa-solid fa-building-circle-check"></i>
                    </div>
                </div>
                <div class="metric-progress-container" style="margin-top: 0.5rem;">
                    <div class="metric-progress-fill" style="width: {{ $avgCampusScore }}%;"></div>
                </div>
                <span class="metric-sub">Sanitation & Safety</span>
            </div>
        </div>

        <!-- Personal Supervisor Score -->
        <div class="card metric-card card-emerald animate-fade-in delay-300">
            <div class="metric-card-content">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                    <div class="metric-info">
                        <span class="metric-label">My Score</span>
                        <span class="metric-value">{{ $personalScore > 0 ? $personalScore . '%' : 'N/A' }}</span>
                    </div>
                    <div class="metric-icon-box">
                        <i class="fa-solid fa-award"></i>
                    </div>
                </div>
                <div class="metric-progress-container" style="margin-top: 0.5rem;">
                    <div class="metric-progress-fill" style="width: {{ $personalScore > 0 ? $personalScore : 0 }}%;"></div>
                </div>
                <span class="metric-sub">Super Admin evaluation</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="animate-fade-in delay-100" style="margin-top: 0.5rem;">
        <h3
            style="margin-bottom: 1.25rem; font-size: 1.25rem; display: flex; align-items: center; gap: 0.5rem; font-family: var(--font-heading); font-weight: 600;">
            Quick Actions
        </h3>
        <div class="action-grid">
            <a href="{{ route('admin.teacher-inspection') }}" class="action-card card-indigo">
                <div class="action-card-icon">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div class="action-card-title">Evaluate Teacher Lecture</div>
                <div class="action-card-desc">Review class lectures and score pedagogical methods and lesson plan alignment.
                </div>
                <div class="action-card-arrow"><i class="fa-solid fa-arrow-right"></i></div>
            </a>

            <a href="{{ route('admin.campus-inspection') }}" class="action-card card-emerald">
                <div class="action-card-icon">
                    <i class="fa-solid fa-building-circle-check"></i>
                </div>
                <div class="action-card-title">Perform Campus Inspection</div>
                <div class="action-card-desc">Audit safety protocols, facility cleanliness, and sanitation conditions.</div>
                <div class="action-card-arrow"><i class="fa-solid fa-arrow-right"></i></div>
            </a>

            <a href="{{ route('admin.teachers') }}" class="action-card card-cyan">
                <div class="action-card-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="action-card-title">Manage Teachers List</div>
                <div class="action-card-desc">View teacher list, historical scores, profiles, and assignment statuses.</div>
                <div class="action-card-arrow"><i class="fa-solid fa-arrow-right"></i></div>
            </a>
        </div>
    </div>

    {{-- Recent Inspections Log --}}
    <div class="card animate-fade-in delay-200" style="margin-top: 0.5rem;">
        <div class="section-header">
            <div class="section-icon" style="--icon-bg: var(--secondary-glow); --icon-color: var(--secondary);">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div>
                <h3 class="section-title">Recent Activities</h3>
                <p class="section-subtitle">Latest inspections conducted by you</p>
            </div>
        </div>

        {{-- Desktop table (hidden on mobile) --}}
        <div class="table-container desktop-only">
            <table>
                <thead>
                    <tr>
                        <th>Date Logged</th>
                        <th>Teacher</th>
                        <th>Class / Lecture</th>
                        <th>Score</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentInspections as $ins)
                        @php $scoreClass = $ins->score >= 85 ? 'badge-success' : ($ins->score >= 70 ? 'badge-warning' : 'badge-danger'); @endphp
                        <tr>
                            <td style="color:var(--text-secondary);font-size:0.9rem;">
                                <div style="display:inline-flex;align-items:center;gap:0.5rem;">
                                    <i class="fa-regular fa-calendar" style="color:var(--text-muted);"></i>
                                    {{ $ins->created_at->format('M d, Y') }}
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:0.75rem;">
                                    <div class="profile-avatar" style="width:32px;height:32px;font-size:0.8rem;font-weight:700;">
                                        {{ strtoupper(substr($ins->teacher->name ?? 'D', 0, 1)) }}
                                    </div>
                                    <span style="font-weight:600;">{{ $ins->teacher->name ?? 'Deleted Teacher' }}</span>
                                </div>
                            </td>
                            <td style="color:var(--text-secondary);">
                                <span style="background:rgba(255,255,255,0.03);border:1px solid var(--border-color);padding:0.3rem 0.6rem;border-radius:8px;font-size:0.85rem;">
                                    {{ $ins->campusClass->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td><span class="badge {{ $scoreClass }}">{{ $ins->score }}%</span></td>
                            <td style="text-align:right;">
                                <a href="{{ route('admin.inspections.view', $ins->id) }}" class="btn btn-secondary"
                                    style="padding:0.45rem 0.9rem;font-size:0.85rem;border-radius:10px;gap:0.35rem;">
                                    View <i class="fa-solid fa-arrow-right" style="font-size:0.75rem;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;color:var(--text-muted);padding:4rem 2rem;">
                                <i class="fa-regular fa-folder-open" style="font-size:2.5rem;margin-bottom:1.25rem;opacity:0.4;display:block;"></i>
                                No inspections conducted recently.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile activity log (hidden on desktop) --}}
        <div class="activity-list mobile-only">
            @forelse($recentInspections as $ins)
                @php $scoreClass = $ins->score >= 85 ? 'badge-success' : ($ins->score >= 70 ? 'badge-warning' : 'badge-danger'); @endphp
                <a href="{{ route('admin.inspections.view', $ins->id) }}" class="activity-item activity-item-link">
                    <div class="activity-score-ring {{ $ins->score >= 85 ? 'ring-good' : ($ins->score >= 70 ? 'ring-avg' : 'ring-low') }}">
                        {{ $ins->score }}
                    </div>
                    <div class="activity-details">
                        <div class="activity-title">{{ $ins->teacher->name ?? 'Deleted Teacher' }}</div>
                        <div class="activity-meta">
                            <span><i class="fa-solid fa-graduation-cap"></i> {{ $ins->campusClass->name ?? 'N/A' }}</span>
                            <span class="dot">·</span>
                            <span><i class="fa-regular fa-clock"></i> {{ $ins->created_at->format('M d') }}</span>
                        </div>
                    </div>
                    <span class="badge {{ $scoreClass }} activity-badge">{{ $ins->score }}%</span>
                </a>
            @empty
                <div class="activity-empty">
                    <i class="fa-regular fa-folder-open"></i>
                    <p>No inspections yet. Start by evaluating a teacher.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection