@extends('layouts.app')

@section('title', 'Campus Safety Inspection - TPA')
@section('header_title', 'Campus Safety & Sanitation Inspection')
@section('header_subtitle', 'Assess cleanliness, physical resources, and exit guidelines compliance')

@section('content')
    <div class="card" style="display: flex; flex-direction: column; gap: 1.5rem;">
        <h3>Checklist Form: {{ auth()->user()->campus->name ?? 'My Campus' }}</h3>

        <form action="{{ route('admin.campus-inspection.submit') }}" method="POST">
            @csrf

            {{-- Criteria & Questions --}}
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                @forelse($criteria as $crit)
                    <div class="criteria-block">
                        <h4 class="criteria-block-title">{{ $crit->name }}</h4>
                        @if($crit->description)
                            <p class="criteria-block-desc">{{ $crit->description }}</p>
                        @endif

                        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                            @foreach($crit->subCriteria as $sub)
                                <div class="sub-criteria-block">
                                    <h5 class="sub-criteria-title">{{ $sub->name }}</h5>

                                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                                        @foreach($sub->questions as $q)
                                            <div class="question-item">
                                                <p class="question-item-text">{{ $q->question_text }}</p>

                                                {{-- Scoring & Comment --}}
                                                <div class="question-score-grid">
                                                    {{-- Score Slider --}}
                                                    <div class="slider-row">
                                                        <input type="range" class="score-slider"
                                                            name="scores[{{ $q->id }}]"
                                                            id="q_slider_{{ $q->id }}"
                                                            min="0" max="{{ $q->max_score }}"
                                                            value="{{ round($q->max_score / 2) }}"
                                                            oninput="updateSliderVal({{ $q->id }})">
                                                        <span class="slider-value" id="q_val_{{ $q->id }}">{{ round($q->max_score / 2) }}</span>
                                                        <span class="slider-max-label">/ {{ $q->max_score }}</span>
                                                    </div>

                                                    {{-- Comment Box --}}
                                                    <input type="text" class="form-control"
                                                        name="comments[{{ $q->id }}]"
                                                        placeholder="Add sanitation comments for this item...">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="card" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                        <i class="fa-solid fa-circle-info" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        <p>No campus inspection criteria found. Ask Super Admin to configure them in
                            <strong>Inspection Config</strong> first.
                        </p>
                    </div>
                @endforelse
            </div>

            @if($criteria->count() > 0)
                <div class="inspection-submit-bar">
                    <button type="submit" class="btn btn-accent" style="padding: 0.85rem 2rem; font-size: 1rem;">
                        <i class="fa-solid fa-paper-plane"></i> Log Inspection Checklist
                    </button>
                </div>
            @endif
        </form>
    </div>

    <script>
        function updateSliderVal(id) {
            const slider = document.getElementById(`q_slider_${id}`);
            const valueBox = document.getElementById(`q_val_${id}`);
            valueBox.innerText = slider.value;
        }
    </script>
@endsection