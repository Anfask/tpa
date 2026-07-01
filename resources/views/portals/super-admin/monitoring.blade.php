@extends('layouts.app')

@section('title', 'Inspection Monitoring - TPA')
@section('header_title', 'Inspection Monitoring')
@section('header_subtitle', 'Global log of all teacher, admin, and campus evaluations')

@section('content')
    <div class="card" style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <h3>Completed Evaluations (All Logs)</h3>

            <!-- Search filter -->
            <div class="search-container">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input class="form-control search-input" type="text" id="search-input" onkeyup="filterTable()"
                    placeholder="Search inspector, teacher, campus...">
            </div>
        </div>

        <div class="table-container">
            <table id="inspections-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Inspected Subject</th>
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
                                <span
                                    class="badge {{ $ins->type === 'teacher' ? 'badge-info' : ($ins->type === 'admin' ? 'badge-success' : 'badge-warning') }}">
                                    {{ ucfirst($ins->type) }}
                                </span>
                            </td>
                            <td>
                                @if($ins->type === 'teacher')
                                    <strong>{{ $ins->teacher->name ?? 'Deleted Teacher' }}</strong>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                                        {{ $ins->campusClass->name ?? 'No Class' }} ({{ $ins->campus->name ?? 'No Campus' }})</div>
                                @elseif($ins->type === 'admin')
                                    <strong>{{ $ins->admin->name ?? 'Deleted Admin' }}</strong>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">Campus:
                                        {{ $ins->campus->name ?? 'No Campus' }}</div>
                                @else
                                    <strong>{{ $ins->campus->name ?? 'Deleted Campus' }}</strong>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">Campus Inspection</div>
                                @endif
                            </td>
                            <td>{{ $ins->inspector->name ?? 'System' }}</td>
                            <td>
                                <strong
                                    style="color: {{ $ins->score >= 85 ? 'var(--accent)' : ($ins->score >= 70 ? 'var(--warning)' : 'var(--danger)') }}">
                                    {{ $ins->score }}%
                                </strong>
                            </td>
                            <td>
                                <a href="{{ route('super-admin.inspections.view', $ins->id) }}" class="btn btn-secondary"
                                    style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                    View Summary
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                No completed inspections found in the database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function filterTable() {
            const input = document.getElementById('search-input');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('inspections-table');
            const tr = table.getElementsByTagName('tr');

            // Loop through all table rows, except the table header row
            for (let i = 1; i < tr.length; i++) {
                let showRow = false;
                const tdArray = tr[i].getElementsByTagName('td');

                // Loop through cells and check if match filter text
                for (let j = 0; j < tdArray.length - 1; j++) { // Skip actions column
                    if (tdArray[j]) {
                        const textValue = tdArray[j].textContent || tdArray[j].innerText;
                        if (textValue.toLowerCase().indexOf(filter) > -1) {
                            showRow = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = showRow ? "" : "none";
            }
        }
    </script>
@endsection