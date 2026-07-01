@extends('layouts.app')

@section('title', 'Campus Details - TPA')
@section('header_title', $campus->name . ' Details')
@section('header_subtitle', 'Overview of classes, personnel, and evaluation history')

@section('content')
<div style="display: flex; gap: 1rem; align-items: center;">
    <a href="{{ route('super-admin.campuses') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to Campuses</a>
    <span class="badge badge-info" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">{{ $campus->address ?? 'No address' }}</span>
</div>

<div class="grid-cols-3">
    <!-- Classes Card -->
    <div class="card" style="display: flex; flex-direction: column; gap: 1.25rem;">
        <h3><i class="fa-solid fa-graduation-cap"></i> Registered Classes</h3>
        <div style="max-height: 350px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.75rem;">
            @forelse($campus->classes as $c)
                <div class="criteria-item">
                    <div>
                        <strong>{{ $c->name }}</strong>
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2rem;">
                    No classes registered.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Personnel Card -->
    <div class="card" style="display: flex; flex-direction: column; gap: 1.25rem;">
        <h3><i class="fa-solid fa-users"></i> Campus Members</h3>
        <div style="max-height: 350px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.75rem;">
            @forelse($campus->users as $u)
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background: rgba(var(--text-muted), 0.02); border: 1px solid var(--border-color); border-radius: 12px;">
                    <div>
                        <strong>{{ $u->name }}</strong>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $u->email }}</div>
                    </div>
                    <span class="badge {{ $u->role === 'admin' ? 'badge-success' : 'badge-info' }}">
                        {{ ucfirst($u->role) }}
                    </span>
                </div>
            @empty
                <div style="text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2rem;">
                    No personnel assigned.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Stats Card -->
    <div class="card">
        <h3><i class="fa-solid fa-chart-pie"></i> Evaluation Overview</h3>
        <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 500;">Total Evaluations Done:</span>
                <span class="score-badge" style="font-size: 1.25rem;">{{ $inspections->count() }}</span>
            </div>
            
            @php
                $avgScore = round($inspections->avg('score') ?? 0, 1);
            @endphp
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 500;">Average Campus Score:</span>
                <span class="score-badge" style="font-size: 1.25rem; background: var(--accent-glow); color: var(--accent);">
                    {{ $avgScore }}%
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Historical Inspections -->
<div class="card">
    <h3 style="margin-bottom: 1.25rem;"><i class="fa-solid fa-history"></i> Campus Inspections History</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Target Member / Entity</th>
                    <th>Inspector</th>
                    <th>Score (%)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inspections as $ins)
                    <tr>
                        <td>{{ $ins->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <span class="badge {{ $ins->type === 'teacher' ? 'badge-info' : ($ins->type === 'admin' ? 'badge-success' : 'badge-warning') }}">
                                {{ ucfirst($ins->type) }}
                            </span>
                        </td>
                        <td>
                            @if($ins->type === 'teacher')
                                <strong>{{ $ins->teacher->name ?? 'Deleted Teacher' }}</strong>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $ins->campusClass->name ?? 'No Class' }}</div>
                            @elseif($ins->type === 'admin')
                                <strong>{{ $ins->admin->name ?? 'Deleted Admin' }}</strong>
                            @else
                                <strong>{{ $ins->campus->name ?? 'Deleted Campus' }}</strong>
                            @endif
                        </td>
                        <td>{{ $ins->inspector->name ?? 'System' }}</td>
                        <td>
                            <strong style="color: {{ $ins->score >= 85 ? 'var(--accent)' : ($ins->score >= 70 ? 'var(--warning)' : 'var(--danger)') }}">
                                {{ $ins->score }}%
                            </strong>
                        </td>
                        <td>
                            <a href="{{ route('super-admin.inspections.view', $ins->id) }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                <i class="fa-solid fa-file-invoice"></i> View Summary
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            No historical inspections found for this campus.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
