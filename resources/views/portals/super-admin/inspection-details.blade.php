@extends('layouts.app')

@section('title', 'Inspection Detail Summary - TPA')
@section('header_title', 'Inspection Report Sheet')
@section('header_subtitle', 'Breakdown of evaluation points and criteria scoring')

@section('content')
<div style="display: flex; gap: 1rem; align-items: center;">
    <a href="{{ route('super-admin.monitoring') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to Logs</a>
    <span class="badge badge-info" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">Log ID: #{{ $inspection->id }}</span>
</div>

<div class="inspection-detail-grid">
    <!-- Left Column: Inspection Overview -->
    <div class="card" style="display: flex; flex-direction: column; gap: 1.5rem; height: fit-content;">
        <div style="text-align: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
            <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">
                Evaluation Grade
            </div>
            <div class="score-badge" style="font-size: 2.75rem; padding: 0.5rem 1.25rem; border-radius: 16px;">
                {{ $inspection->score }}%
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div>
                <label style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">
                    Date & Time
                </label>
                <div style="font-weight: 500;">{{ $inspection->created_at->format('F j, Y, g:i a') }}</div>
            </div>

            <div>
                <label style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">
                    Inspector
                </label>
                <div style="font-weight: 500;">{{ $inspection->inspector->name ?? 'System' }}</div>
            </div>

            <div>
                <label style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">
                    Evaluation Class Target
                </label>
                <div style="font-weight: 500; text-transform: capitalize;">{{ $inspection->type }} Inspection</div>
            </div>

            @if($inspection->type === 'teacher')
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">
                        Teacher Evaluated
                    </label>
                    <div style="font-weight: 500;">{{ $inspection->teacher->name ?? 'Deleted Teacher' }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary);">Class: {{ $inspection->campusClass->name ?? 'None' }}</div>
                </div>
            @elseif($inspection->type === 'admin')
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">
                        Administrator Evaluated
                    </label>
                    <div style="font-weight: 500;">{{ $inspection->admin->name ?? 'Deleted Admin' }}</div>
                </div>
            @endif

            @if($inspection->campus)
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">
                        Campus Location
                    </label>
                    <div style="font-weight: 500;">{{ $inspection->campus->name }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Question Answers Breakdown -->
    <div class="card" style="display: flex; flex-direction: column; gap: 1.5rem;">
        <h3>Question Score breakdown</h3>

        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @foreach($answers as $ans)
                <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem;">
                    <div class="answer-item-header">
                        <div>
                            <span class="badge badge-info" style="font-size: 0.65rem; margin-bottom: 0.35rem; display: inline-block;">
                                {{ $ans['criteria'] }} → {{ $ans['sub_criteria'] }}
                            </span>
                            <h4 style="font-size: 1rem; font-weight: 500; color: var(--text-primary);">
                                {{ $ans['question_text'] }}
                            </h4>
                        </div>
                        <div style="display: flex; align-items: baseline; gap: 0.15rem; font-size: 1.2rem; font-weight: 700; color: var(--primary);">
                            <span>{{ $ans['score'] }}</span>
                            <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">/ {{ $ans['max_score'] }}</span>
                        </div>
                    </div>
                    
                    @if($ans['comment'])
                        <div style="padding: 0.75rem; border-radius: 8px; background: rgba(var(--text-muted), 0.03); border: 1px solid var(--border-color); font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.5rem;">
                            <strong>Remarks:</strong> {{ $ans['comment'] }}
                        </div>
                    @else
                        <div style="font-size: 0.8rem; color: var(--text-muted); font-style: italic; margin-top: 0.5rem;">
                            No comment provided.
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
