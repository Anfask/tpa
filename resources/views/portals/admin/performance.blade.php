@extends('layouts.app')

@section('title', 'My Performance Evaluation - TPA')
@section('header_title', 'My Performance Evaluation')
@section('header_subtitle', 'Review supervisor evaluations and leadership score records')

@section('content')
<div class="card" style="display: flex; flex-direction: column; gap: 1.5rem;">
    <h3>Super Admin Evaluations</h3>
    <p style="font-size: 0.85rem; color: var(--text-secondary);">This log shows performance feedback and scores filed by the Super Admin during leadership audits.</p>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date Logged</th>
                    <th>Evaluated By</th>
                    <th>Score (%)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evaluations as $eval)
                    <tr>
                        <td>{{ $eval->created_at->format('F j, Y, g:i a') }}</td>
                        <td><strong>{{ $eval->inspector->name ?? 'Super Admin' }}</strong></td>
                        <td>
                            <span class="score-badge" style="font-size: 1.05rem; padding: 0.2rem 0.6rem;">
                                {{ $eval->score }}%
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.inspections.view', $eval->id) }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                <i class="fa-solid fa-file-invoice"></i> View Full Breakdown
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">
                            No evaluations logged for your account yet. Check back later.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
