@extends('layouts.app')

@section('title', 'Teacher Management - TPA')
@section('header_title', 'Teacher Management')
@section('header_subtitle', 'Add, modify, and monitor teacher evaluations across campuses')

@section('content')
    <div class="grid-cols-3" style="grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <!-- Teachers List -->
        <div class="card" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <h3>Registered Teachers</h3>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Teacher Name</th>
                            <th>Campus Name</th>
                            <th>Avg Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                            <tr>
                                <td>
                                    <strong>{{ $teacher->name }}</strong>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $teacher->email }}</div>
                                </td>
                                <td>
                                    @if($teacher->campus)
                                        <strong>{{ $teacher->campus->name }}</strong>
                                    @else
                                        <strong>Unassigned</strong>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $score = $teacher_scores[$teacher->id];
                                    @endphp
                                    @if($score > 0)
                                        <strong
                                            style="color: {{ $score >= 85 ? 'var(--accent)' : ($score >= 70 ? 'var(--warning)' : 'var(--danger)') }}">
                                            {{ $score }}%
                                        </strong>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.8rem;">No evaluations</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button class="btn-icon" onclick="openEditModal({{ json_encode($teacher) }})"
                                            title="Edit Teacher" style="font-size: 0.9rem; color: var(--primary);">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('super-admin.teachers.delete', $teacher->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this teacher account? This will delete all remarks and scores associated.');">
                                            @csrf
                                            <button type="submit" class="btn-icon" title="Delete Teacher"
                                                style="font-size: 0.9rem; color: var(--danger);">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    No teachers registered. Register one to log inspections.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Add/Edit Panel -->
        <div class="card" style="align-self: flex-start;">
            <h3 id="panel-title" style="margin-bottom: 1.5rem;"> Add New Teacher</h3>

            <form id="teacher-form" action="{{ route('super-admin.teachers.add') }}" method="POST">
                @csrf
                <input type="hidden" id="edit-id" name="id">

                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input class="form-control" type="text" id="name" name="name" placeholder="Full Name" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input class="form-control" type="email" id="email" name="email" placeholder="Email Address" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="campus_id">Assign Campus</label>
                    <select class="form-control" id="campus_id" name="campus_id" required>
                        <option value="">-- Select Campus --</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="password-group" style="display: none;">
                    <label class="form-label" for="password">New Password</label>
                    <input class="form-control" type="password" id="password" name="password" placeholder="Leave blank to keep current">
                    <span id="password-hint" style="font-size: 0.75rem; color: var(--text-muted);">Leave blank to keep current password.</span>
                </div>

                <div id="auto-password-notice" style="font-size:0.82rem; color:var(--accent); background:var(--accent-glow); border:1px solid rgba(16,185,129,0.2); border-radius:10px; padding:0.65rem 0.9rem; display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
                    <i class="fa-solid fa-lock-open" style="font-size:0.9rem;"></i>
                    A secure temporary password will be auto-generated and emailed to the teacher.
                </div>

                <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                    <button type="submit" class="btn btn-primary" style="flex-grow: 1; justify-content: center;">
                        <span id="btn-text">Create Account</span>
                    </button>
                    <button type="button" id="btn-cancel" class="btn btn-secondary" style="display: none;"
                        onclick="resetForm()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('teacher-form');
        const panelTitle = document.getElementById('panel-title');
        const btnText = document.getElementById('btn-text');
        const btnCancel = document.getElementById('btn-cancel');
        const editIdInput = document.getElementById('edit-id');
        const passwordHint = document.getElementById('password-hint');

        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const campusSelect = document.getElementById('campus_id');
        const passwordInput = document.getElementById('password');

        function openEditModal(teacher) {
            panelTitle.innerHTML = '<i class="fa-solid fa-user-pen"></i> Edit Teacher Account';
            btnText.innerText = 'Save Changes';
            btnCancel.style.display = 'inline-flex';

            document.getElementById('password-group').style.display = 'flex';
            document.getElementById('auto-password-notice').style.display = 'none';

            editIdInput.value = teacher.id;
            nameInput.value = teacher.name;
            emailInput.value = teacher.email;
            campusSelect.value = teacher.campus_id || '';
            if (passwordInput) passwordInput.value = '';

            form.action = `/super-admin/teachers/${teacher.id}/edit`;
        }

        function resetForm() {
            panelTitle.innerHTML = '<i class="fa-solid fa-user-plus"></i> Add New Teacher';
            btnText.innerText = 'Create Account';
            btnCancel.style.display = 'none';

            document.getElementById('password-group').style.display = 'none';
            document.getElementById('auto-password-notice').style.display = 'flex';

            editIdInput.value = '';
            nameInput.value = '';
            emailInput.value = '';
            campusSelect.value = '';
            if (passwordInput) passwordInput.value = '';

            form.action = "{{ route('super-admin.teachers.add') }}";
        }
    </script>
@endsection