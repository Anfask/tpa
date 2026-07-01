@extends('layouts.app')

@section('title', 'Class Management - TPA')
@section('header_title', 'Class Management')
@section('header_subtitle', 'Setup classes to categorize and contextualize teacher inspections')

@section('content')
    <div class="grid-cols-3" style="grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <!-- Classes List -->
        <div class="card" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <h3>Registered Classes</h3>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Class Name</th>
                            <th>Campus Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $class)
                            <tr>
                                <td><strong>{{ $class->name }}</strong></td>
                                <td>{{ $class->campus->name ?? 'Unassigned Campus' }}</td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button class="btn-icon" onclick="openEditModal({{ json_encode($class) }})"
                                            title="Edit Class" style="font-size: 0.9rem; color: var(--primary);">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('super-admin.classes.delete', $class->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this class? This will dissociate inspections.');">
                                            @csrf
                                            <button type="submit" class="btn-icon" title="Delete Class"
                                                style="font-size: 0.9rem; color: var(--danger);">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    No classes registered. Create one to assign teacher lectures.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Add/Edit Panel -->
        <div class="card" style="align-self: flex-start;">
            <h3 id="panel-title" style="margin-bottom: 1.5rem;"> Add New Class</h3>

            <form id="class-form" action="{{ route('super-admin.classes.add') }}" method="POST">
                @csrf
                <input type="hidden" id="edit-id" name="id">

                <div class="form-group">
                    <label class="form-label" for="name">Class Name</label>
                    <input class="form-control" type="text" id="name" name="name" placeholder="Class Name" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="campus_id">Campus Location</label>
                    <select class="form-control" id="campus_id" name="campus_id" required>
                        <option value="">-- Select Campus --</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                    <button type="submit" class="btn btn-primary" style="flex-grow: 1; justify-content: center;">
                        <span id="btn-text">Add Class</span>
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
        const form = document.getElementById('class-form');
        const panelTitle = document.getElementById('panel-title');
        const btnText = document.getElementById('btn-text');
        const btnCancel = document.getElementById('btn-cancel');
        const editIdInput = document.getElementById('edit-id');

        const nameInput = document.getElementById('name');
        const campusSelect = document.getElementById('campus_id');

        function openEditModal(cls) {
            panelTitle.innerHTML = '<i class="fa-solid fa-edit"></i> Edit Class';
            btnText.innerText = 'Save Changes';
            btnCancel.style.display = 'inline-flex';

            editIdInput.value = cls.id;
            nameInput.value = cls.name;
            campusSelect.value = cls.campus_id || '';

            form.action = `/super-admin/classes/${cls.id}/edit`;
        }

        function resetForm() {
            panelTitle.innerHTML = '<i class="fa-solid fa-plus-circle"></i> Add New Class';
            btnText.innerText = 'Add Class';
            btnCancel.style.display = 'none';

            editIdInput.value = '';
            nameInput.value = '';
            campusSelect.value = '';

            form.action = "{{ route('super-admin.classes.add') }}";
        }
    </script>
@endsection