@extends('layouts.app')

@section('title', 'My Profile - TPA')
@section('header_title', 'My Profile')
@section('header_subtitle', 'Personal details, campus assignment, and admin feedback')

@section('content')
    <div class="grid-cols-3" style="grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        <!-- Profile Card -->
        <div class="card"
            style="display: flex; flex-direction: column; gap: 1.25rem; align-items: center; text-align: center; align-self: flex-start;">
            <div class="profile-avatar" style="width: 80px; height: 80px; font-size: 2rem;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h3 style="font-size: 1.3rem;">{{ $user->name }}</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted);">{{ $user->email }}</p>
            </div>

            <div
                style="width: 100%; border-top: 1px solid var(--border-color); padding-top: 1rem; display: flex; flex-direction: column; gap: 0.75rem; text-align: left;">
                <div>
                    <label
                        style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Role</label>
                    <div style="font-weight: 500;">Teacher</div>
                </div>
                <div>
                    <label
                        style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Campus</label>
                    <div style="font-weight: 500;">{{ $user->campus->name ?? 'Unassigned' }}</div>
                    @if($user->campus && $user->campus->address)
                        <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $user->campus->address }}</div>
                    @endif
                </div>
                <div>
                    <label
                        style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Member
                        Since</label>
                    <div style="font-weight: 500;">{{ $user->created_at->format('F j, Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Remarks from Admins (Public Only) -->
        <div class="card" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <h3>Admin Feedback & Remarks</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary);">Observations and comments left by your campus
                administrator.</p>

            <div
                style="display: flex; flex-direction: column; gap: 1rem; max-height: 500px; overflow-y: auto; padding-right: 0.5rem;">
                @forelse($remarks as $rem)
                    <div
                        style="padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color); background: rgba(16, 185, 129, 0.03);">
                        <div
                            style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-primary);">
                                {{ $rem->inspector->name ?? 'Admin' }}
                            </span>
                            <span style="font-size: 0.7rem; color: var(--text-muted);">
                                {{ $rem->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.5;">
                            {{ $rem->content }}
                        </p>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2.5rem;">
                        No public feedback from your admin yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection