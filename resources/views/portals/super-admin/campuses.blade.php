@extends('layouts.app')

@section('title', 'Campus Management - TPA')
@section('header_title', 'Campus Management')
@section('header_subtitle', 'Manage education nodes, codes, and locations')

@section('content')
    <div class="grid-cols-3" style="grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <!-- Campuses List -->
        <div class="card" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <h3>Registered Campuses</h3>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Campus Name</th>
                            <th>Location</th>
                            <th>Classes</th>
                            <th>Personnel</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campuses as $campus)
                            <tr>
                                <td><strong>{{ $campus->name }}</strong></td>
                                <td>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">
                                        {{ $campus->address ?? 'No address' }}
                                    </div>
                                </td>
                                <td>{{ $campus->classes_count }} Classes</td>
                                <td>{{ $campus->users_count }} Members</td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="{{ route('super-admin.campuses.view', $campus->id) }}" class="btn-icon"
                                            title="View Details" style="font-size: 0.9rem;">
                                            <i class="fa-solid fa-folder-open"></i>
                                        </a>
                                        <button class="btn-icon" onclick="openEditModal({{ json_encode($campus) }})"
                                            title="Edit Campus" style="font-size: 0.9rem; color: var(--primary);">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('super-admin.campuses.delete', $campus->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this campus? This will delete all classes and reassign teachers.');">
                                            @csrf
                                            <button type="submit" class="btn-icon" title="Delete Campus"
                                                style="font-size: 0.9rem; color: var(--danger);">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    No campuses found. Create one to begin.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: Add / Edit Panel -->
        <div class="card" style="align-self: flex-start;">
            <h3 id="panel-title" style="margin-bottom: 1.5rem;">Add New Campus</h3>

            <form id="campus-form" action="{{ route('super-admin.campuses.add') }}" method="POST">
                @csrf
                <input type="hidden" id="edit-id" name="id">

                <div class="form-group">
                    <label class="form-label" for="name">Campus Name</label>
                    <input class="form-control" type="text" id="name" name="name" placeholder="Campus Name" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Address / Location</label>
                    <input class="form-control" type="text" id="address" name="address" placeholder="Campus Address">
                </div>

                <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                    <button type="submit" class="btn btn-primary" style="flex-grow: 1; justify-content: center;">
                        <span id="btn-text">Add Campus</span>
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
        const form = document.getElementById('campus-form');
        const panelTitle = document.getElementById('panel-title');
        const btnText = document.getElementById('btn-text');
        const btnCancel = document.getElementById('btn-cancel');
        const editIdInput = document.getElementById('edit-id');

        const nameInput = document.getElementById('name');
        const addressInput = document.getElementById('address');

        function openEditModal(campus) {
            panelTitle.innerHTML = '<i class="fa-solid fa-edit"></i> Edit Campus';
            btnText.innerText = 'Save Changes';
            btnCancel.style.display = 'inline-flex';

            editIdInput.value = campus.id;
            nameInput.value = campus.name;
            addressInput.value = campus.address || '';

            form.action = `/super-admin/campuses/${campus.id}/edit`;
        }

        function resetForm() {
            panelTitle.innerHTML = '<i class="fa-solid fa-plus-circle"></i> Add New Campus';
            btnText.innerText = 'Add Campus';
            btnCancel.style.display = 'none';

            editIdInput.value = '';
            nameInput.value = '';
            addressInput.value = '';

            form.action = "{{ route('super-admin.campuses.add') }}";
        }
    </script>
@endsection