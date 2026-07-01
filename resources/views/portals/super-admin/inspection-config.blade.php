@extends('layouts.app')

@section('title', 'Inspection Configuration - TPA')
@section('header_title', 'Inspection Configurator')
@section('header_subtitle', 'Configure evaluation criteria, sub-criteria, and question banks')

@section('content')
<div class="grid-cols-3" style="grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Criteria Structure Builder -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        @foreach(['teacher' => 'Teacher Evaluation Bank', 'admin' => 'Admin Evaluation Bank', 'campus' => 'Campus Evaluation Bank'] as $type => $title)
            <div class="card" style="display: flex; flex-direction: column; gap: 1rem;">
                <h3 style="border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; display: flex; align-items: center; justify-content: space-between;">
                    <span>{{ $title }}</span>
                    <span class="badge badge-info" style="font-size: 0.75rem;">{{ ucfirst($type) }}</span>
                </h3>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @php
                        $filteredCriteria = $criteria->where('type', $type);
                    @endphp
                    @forelse($filteredCriteria as $crit)
                        <div style="border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem; background: rgba(var(--text-secondary), 0.01)">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                <div style="display: flex; flex-direction: column; width: 100%;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; justify-content: space-between; width: 100%;">
                                        <h4 style="font-size: 1.05rem; color: var(--primary);">{{ $crit->name }}</h4>
                                        <button class="btn-edit-criteria" 
                                                data-id="{{ $crit->id }}" 
                                                data-name="{{ $crit->name }}" 
                                                data-type="{{ $crit->type }}" 
                                                data-description="{{ $crit->description }}" 
                                                style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 0.85rem; padding: 0; display: inline-flex;" 
                                                title="Edit Criteria">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </div>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.25rem;">{{ $crit->description }}</p>
                                </div>
                            </div>

                            <!-- Sub Criteria Loop -->
                            <div style="margin-left: 1rem; display: flex; flex-direction: column; gap: 0.75rem; margin-top: 0.75rem;">
                                @forelse($crit->subCriteria as $sub)
                                    <div style="border-left: 2px solid var(--primary); padding-left: 0.75rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <h5 style="font-size: 0.95rem; color: var(--text-primary);">{{ $sub->name }}</h5>
                                            <button class="btn-edit-sub" 
                                                    data-id="{{ $sub->id }}" 
                                                    data-name="{{ $sub->name }}" 
                                                    data-criteria-id="{{ $sub->criteria_id }}" 
                                                    data-description="{{ $sub->description }}" 
                                                    style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 0.8rem; padding: 0; display: inline-flex;" 
                                                    title="Edit Sub-Criteria">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </div>
                                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.5rem;">{{ $sub->description }}</p>
                                        
                                        <!-- Questions Loop -->
                                        <div style="display: flex; flex-direction: column; gap: 0.4rem; margin-top: 0.25rem;">
                                            @forelse($sub->questions->sortBy('order_index') as $q)
                                                <div class="criteria-item" style="padding: 0.5rem 0.75rem; margin-bottom: 0px; font-size: 0.85rem; background: var(--bg-card); display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-grow: 1; min-width: 0; padding-right: 0.5rem;">
                                                        <span style="font-weight: 700; color: var(--text-muted); flex-shrink: 0;">#{{ $q->order_index }}</span>
                                                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $q->question_text }}">{{ $q->question_text }}</span>
                                                        <span class="badge badge-info" style="font-size: 0.65rem; padding: 0.15rem 0.4rem; flex-shrink: 0;">Max: {{ $q->max_score }}</span>
                                                    </div>
                                                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0;">
                                                        <button class="btn-edit-question" 
                                                                data-id="{{ $q->id }}" 
                                                                data-text="{{ $q->question_text }}" 
                                                                data-sub-id="{{ $q->sub_criteria_id }}" 
                                                                data-max-score="{{ $q->max_score }}" 
                                                                style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 0.85rem;" 
                                                                title="Edit Question">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </button>
                                                        <form action="{{ route('super-admin.questions.delete', $q->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this question?');" style="margin: 0; display: inline-flex;">
                                                            @csrf
                                                            <button type="submit" style="background: none; border: none; color: var(--danger); cursor: pointer;" title="Delete Question">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @empty
                                                <div style="font-size: 0.75rem; color: var(--text-muted); font-style: italic; padding: 0.25rem;">
                                                    No questions created under this sub-criteria yet.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                @empty
                                    <div style="font-size: 0.8rem; color: var(--text-muted); font-style: italic;">
                                        No sub-criteria configured.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 1rem 0;">
                            No parent criteria configured for this evaluation bank.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <!-- Right Side Config Panels -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem; align-self: flex-start;">
        <!-- Add Criteria -->
        <div class="card">
            <h3> Add Criteria</h3>
            <form action="{{ route('super-admin.criteria.add') }}" method="POST" style="margin-top: 1rem;">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="criteria_name">Criteria Name</label>
                    <input class="form-control" type="text" id="criteria_name" name="name" placeholder="Criteria Name" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="criteria_type">Evaluation Target</label>
                    <select class="form-control" id="criteria_type" name="type" required>
                        <option value="teacher">Teacher Evaluation</option>
                        <option value="admin">Admin Evaluation</option>
                        <option value="campus">Campus Evaluation</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="criteria_desc">Description</label>
                    <textarea class="form-control" id="criteria_desc" name="description" placeholder="Describe the criteria target..." rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 0.5rem;">
                    Create Criteria
                </button>
            </form>
        </div>

        <!-- Add Sub-Criteria -->
        <div class="card">
            <h3>Add Sub-Criteria</h3>
            <form action="{{ route('super-admin.sub-criteria.add') }}" method="POST" style="margin-top: 1rem;">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="parent_criteria">Parent Criteria</label>
                    <select class="form-control" id="parent_criteria" name="criteria_id" required>
                        <option value="">-- Select Parent --</option>
                        @foreach($criteria as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ ucfirst($c->type) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="sub_name">Sub-Criteria Name</label>
                    <input class="form-control" type="text" id="sub_name" name="name" placeholder="Sub-Criteria" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="sub_desc">Description</label>
                    <textarea class="form-control" id="sub_desc" name="description" placeholder="Describe specific objectives..." rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 0.5rem;">
                    Create Sub-Criteria
                </button>
            </form>
        </div>

        <!-- Add Question -->
        <div class="card">
            <h3>Add Question</h3>
            <form action="{{ route('super-admin.questions.add') }}" method="POST" style="margin-top: 1rem;">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="parent_sub">Target Sub-Criteria</label>
                    <select class="form-control" id="parent_sub" name="sub_criteria_id" required>
                        <option value="">-- Select Target --</option>
                        @foreach($criteria as $c)
                            @foreach($c->subCriteria as $s)
                                <option value="{{ $s->id }}">{{ $c->name }} → {{ $s->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="q_text">Question Text</label>
                    <textarea class="form-control" id="q_text" name="question_text" placeholder="Question" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="max_score">Max Score (Points)</label>
                    <input class="form-control" type="number" id="max_score" name="max_score" value="10" min="1" max="100" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 0.5rem;">
                    Add Question to Bank
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Edit Criteria Modal -->
<div id="modal-edit-criteria" class="config-modal-overlay">
    <div class="config-modal animate-fade-in">
        <h3><i class="fa-solid fa-pen-to-square"></i> Edit Criteria</h3>
        <form id="form-edit-criteria" method="POST" style="display: flex; flex-direction: column; gap: 1rem; margin-top: 0.5rem;">
            @csrf
            <div class="form-group">
                <label class="form-label" for="edit_crit_name">Criteria Name</label>
                <input class="form-control" type="text" id="edit_crit_name" name="name" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="edit_crit_type">Evaluation Target</label>
                <select class="form-control" id="edit_crit_type" name="type" required>
                    <option value="teacher">Teacher Evaluation</option>
                    <option value="admin">Admin Evaluation</option>
                    <option value="campus">Campus Evaluation</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="edit_crit_desc">Description</label>
                <textarea class="form-control" id="edit_crit_desc" name="description" rows="2"></textarea>
            </div>
            <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-edit-criteria')" style="flex: 1; justify-content: center;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Sub-Criteria Modal -->
<div id="modal-edit-sub" class="config-modal-overlay">
    <div class="config-modal animate-fade-in">
        <h3><i class="fa-solid fa-pen-to-square"></i> Edit Sub-Criteria</h3>
        <form id="form-edit-sub" method="POST" style="display: flex; flex-direction: column; gap: 1rem; margin-top: 0.5rem;">
            @csrf
            <div class="form-group">
                <label class="form-label" for="edit_sub_parent">Parent Criteria</label>
                <select class="form-control" id="edit_sub_parent" name="criteria_id" required>
                    @foreach($criteria as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ ucfirst($c->type) }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="edit_sub_name">Sub-Criteria Name</label>
                <input class="form-control" type="text" id="edit_sub_name" name="name" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="edit_sub_desc">Description</label>
                <textarea class="form-control" id="edit_sub_desc" name="description" rows="2"></textarea>
            </div>
            <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-edit-sub')" style="flex: 1; justify-content: center;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Question Modal -->
<div id="modal-edit-question" class="config-modal-overlay">
    <div class="config-modal animate-fade-in">
        <h3><i class="fa-solid fa-pen-to-square"></i> Edit Question</h3>
        <form id="form-edit-question" method="POST" style="display: flex; flex-direction: column; gap: 1rem; margin-top: 0.5rem;">
            @csrf
            <div class="form-group">
                <label class="form-label" for="edit_q_parent">Target Sub-Criteria</label>
                <select class="form-control" id="edit_q_parent" name="sub_criteria_id" required>
                    @foreach($criteria as $c)
                        @foreach($c->subCriteria as $s)
                            <option value="{{ $s->id }}">{{ $c->name }} → {{ $s->name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="edit_q_text">Question Text</label>
                <textarea class="form-control" id="edit_q_text" name="question_text" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="edit_q_max">Max Score (Points)</label>
                <input class="form-control" type="number" id="edit_q_max" name="max_score" min="1" max="100" required>
            </div>
            <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-edit-question')" style="flex: 1; justify-content: center;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<style>
.config-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(8, 11, 17, 0.65);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    opacity: 0;
    transition: opacity 0.25s ease;
}
.config-modal-overlay.active {
    display: flex;
    opacity: 1;
}
.config-modal {
    background: var(--bg-card);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    border: 1px solid var(--border-color);
    border-radius: 24px;
    padding: 2.25rem 2rem;
    width: 90%;
    max-width: 500px;
    box-shadow: var(--shadow-lg);
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    transform: scale(0.95);
    transition: transform 0.25s ease;
}
.config-modal-overlay.active .config-modal {
    transform: scale(1);
}
</style>
@endsection

@section('scripts')
<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('active'), 10);
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 250);
        }
    }

    // Close on backdrop click
    document.querySelectorAll('.config-modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                closeModal(overlay.id);
            }
        });
    });

    // Edit Criteria Click
    document.querySelectorAll('.btn-edit-criteria').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const type = btn.getAttribute('data-type');
            const desc = btn.getAttribute('data-description');

            document.getElementById('edit_crit_name').value = name || '';
            document.getElementById('edit_crit_type').value = type || 'teacher';
            document.getElementById('edit_crit_desc').value = desc || '';
            
            const form = document.getElementById('form-edit-criteria');
            form.action = `/super-admin/criteria/${id}/edit`;

            openModal('modal-edit-criteria');
        });
    });

    // Edit Sub-Criteria Click
    document.querySelectorAll('.btn-edit-sub').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const criteriaId = btn.getAttribute('data-criteria-id');
            const desc = btn.getAttribute('data-description');

            document.getElementById('edit_sub_name').value = name || '';
            document.getElementById('edit_sub_parent').value = criteriaId || '';
            document.getElementById('edit_sub_desc').value = desc || '';
            
            const form = document.getElementById('form-edit-sub');
            form.action = `/super-admin/sub-criteria/${id}/edit`;

            openModal('modal-edit-sub');
        });
    });

    // Edit Question Click
    document.querySelectorAll('.btn-edit-question').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const text = btn.getAttribute('data-text');
            const subId = btn.getAttribute('data-sub-id');
            const maxScore = btn.getAttribute('data-max-score');

            document.getElementById('edit_q_text').value = text || '';
            document.getElementById('edit_q_parent').value = subId || '';
            document.getElementById('edit_q_max').value = maxScore || '10';
            
            const form = document.getElementById('form-edit-question');
            form.action = `/super-admin/questions/${id}/edit`;

            openModal('modal-edit-question');
        });
    });
</script>
@endsection
