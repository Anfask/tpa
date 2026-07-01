@extends('layouts.app')

@section('title', 'Teacher Profile - TPA')
@section('header_title', 'Teacher Profile Summary')
@section('header_subtitle', 'Comprehensive file card, evaluations, and remarks history')

@section('content')
<div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 0.5rem;">
    <a href="{{ route('admin.teachers') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to Teachers</a>
</div>

<div class="grid-cols-3" style="grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    
    <!-- Left: Profile Details & Remarks Form -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Profile Card -->
        <div class="card" style="display: flex; flex-direction: column; gap: 1rem; align-items: center; text-align: center;">
            <div class="profile-avatar" style="width: 72px; height: 72px; font-size: 1.8rem;">
                {{ strtoupper(substr($teacher->name, 0, 1)) }}
            </div>
            <div>
                <h3 style="font-size: 1.25rem;">{{ $teacher->name }}</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted);">{{ $teacher->email }}</p>
            </div>
            
            <div style="display: flex; gap: 0.5rem; justify-content: center; width: 100%; border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: 0.5rem;">
                <div style="flex-grow: 1;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Average Rating</div>
                    <div class="score-badge" style="font-size: 1.25rem; margin-top: 0.25rem; display: inline-block;">
                        {{ $avgScore > 0 ? $avgScore . '%' : 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Remarks Form -->
        <div class="card">
            <h3><i class="fa-solid fa-comment-dots"></i> Log Remark</h3>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">File academic feedback or private internal performance observations.</p>
            
            <form action="{{ route('admin.remarks.add') }}" method="POST">
                @csrf
                <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                
                <div class="form-group">
                    <label class="form-label" for="remark_content">Feedback/Observation Notes</label>
                    <textarea class="form-control" id="remark_content" name="content" placeholder="Type comments here..." rows="4" required></textarea>
                </div>

                <div class="form-group" style="flex-direction: row; gap: 0.5rem; align-items: center; margin-bottom: 1.25rem;">
                    <input type="hidden" name="is_private" value="0">
                    <input type="checkbox" id="is_private_checkbox" name="is_private" value="1" style="cursor: pointer; width: 16px; height: 16px;">
                    <label for="is_private_checkbox" style="font-size: 0.85rem; color: var(--text-secondary); cursor: pointer; user-select: none;">
                        <i class="fa-solid fa-lock" style="font-size: 0.75rem;"></i> Mark as Private Internal Note
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    Submit Remark Log
                </button>
            </form>
        </div>
    </div>

    <!-- Right: Tabs (Inspections & Remarks Timeline) -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Inspection History -->
        <div class="card">
            <h3 style="margin-bottom: 1.25rem;"><i class="fa-solid fa-history"></i> Evaluation History</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Class Lecture</th>
                            <th>Inspector</th>
                            <th>Score (%)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inspections as $ins)
                            <tr>
                                <td>{{ $ins->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $ins->campusClass->name ?? 'N/A' }}</td>
                                <td>{{ $ins->inspector->name ?? 'System' }}</td>
                                <td>
                                    <strong style="color: {{ $ins->score >= 85 ? 'var(--accent)' : ($ins->score >= 70 ? 'var(--warning)' : 'var(--danger)') }}">
                                        {{ $ins->score }}%
                                    </strong>
                                </td>
                                <td>
                                    <a href="{{ route('super-admin.inspections.view', $ins->id) }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                        <i class="fa-solid fa-eye"></i> Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    No inspections logged for this teacher.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Remarks History Timeline -->
        <div class="card">
            <h3 style="margin-bottom: 1.25rem;"><i class="fa-solid fa-comments"></i> Remarks & Feedback Timeline</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem; max-height: 400px; overflow-y: auto; padding-right: 0.5rem;">
                @forelse($remarks as $rem)
                    <div style="padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color); background: {{ $rem->is_private ? 'rgba(244, 63, 94, 0.04)' : 'rgba(16, 185, 129, 0.04)' }}; position: relative;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary);">
                                Admin: {{ $rem->inspector->name ?? 'System' }}
                            </span>
                            <span style="font-size: 0.7rem; color: var(--text-muted);">
                                {{ $rem->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.4;">
                            {{ $rem->content }}
                        </p>
                        
                        <div style="margin-top: 0.5rem; display: flex; justify-content: flex-end;">
                            @if($rem->is_private)
                                <span class="badge badge-danger" style="font-size: 0.65rem; border-radius: 4px;">
                                    <i class="fa-solid fa-lock" style="font-size: 0.6rem;"></i> Private Internal Note
                                </span>
                            @else
                                <span class="badge badge-success" style="font-size: 0.65rem; border-radius: 4px;">
                                    <i class="fa-solid fa-globe" style="font-size: 0.6rem;"></i> Public (Visible to Teacher)
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2rem;">
                        No remarks logged yet.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
