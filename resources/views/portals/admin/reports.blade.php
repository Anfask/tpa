@extends('layouts.app')

@section('title', 'Reports & Export - TPA')
@section('header_title', 'Report Center & Export')
@section('header_subtitle', 'Generate and extract performance data records for your campus')

@section('content')
    <div class="animate-fade-in" style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- CSV Export Engine Card -->
        <div class="card" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <h3>Export Data Logs</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary);">Filter campus evaluations and download a structured
                spreadsheet (CSV format).</p>

            <form action="{{ route('admin.reports.export') }}" method="GET" target="_blank"
                style="display: flex; flex-direction: column; gap: 1.25rem; margin-top: 0.5rem;">

                <div class="form-group">
                    <label class="form-label" for="report_type">Evaluation Bank Category</label>
                    <select class="form-control" id="report_type" name="type" onchange="toggleEntitySelects()" required>
                        <option value="teacher">Teacher Inspections</option>
                        <option value="admin">Admin Evaluations</option>
                        <option value="campus">Campus Inspections</option>
                    </select>
                </div>

                <!-- Teacher Select -->
                <div class="form-group" id="teacher-group">
                    <label class="form-label" for="teacher_id">Select Teacher (Optional)</label>
                    <select class="form-control" id="teacher_id" name="entity_id">
                        <option value="">All Teachers</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Admin Select -->
                <div class="form-group" id="admin-group" style="display: none;">
                    <label class="form-label" for="admin_id">Select Administrator</label>
                    <select class="form-control" id="admin_id" name="entity_id" required>
                        @foreach($admins as $a)
                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Campus Select -->
                <div class="form-group" id="campus-group" style="display: none;">
                    <label class="form-label" for="campus_id">Select Campus Node</label>
                    <select class="form-control" id="campus_id" name="entity_id" required>
                        @foreach($campuses as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                <button type="submit" name="format" value="csv" class="btn btn-primary" style="flex: 1; justify-content: center; padding: 0.85rem;">
                    <i class="fa-solid fa-file-csv"></i> Download CSV
                </button>
                <button type="submit" name="format" value="pdf" class="btn btn-accent" style="flex: 1; justify-content: center; padding: 0.85rem;">
                    <i class="fa-solid fa-file-pdf"></i> Download PDF
                </button>
            </div>
            </form>
        </div>

        <!-- Info/Documentation Card -->
        <div class="card" style="display: flex; flex-direction: column; gap: 1rem;">
            <h3>Campus Scope Logs</h3>
            <p style="font-size: 0.9rem; line-height: 1.5; color: var(--text-secondary);">
                Your report views are strictly restricted to evaluations registered for your assigned campus node.
            </p>
            <ul
                style="margin-left: 1.25rem; font-size: 0.85rem; color: var(--text-secondary); display: flex; flex-direction: column; gap: 0.5rem;">
                <li><strong>Teacher reports:</strong> Logs scores, dates, class contexts, and points for teachers in your
                    campus.</li>
                <li><strong>Admin reports:</strong> Logs your own leadership performance evaluation details registered by
                    the Super Admin.</li>
                <li><strong>Campus reports:</strong> Extracts cleanliness and safety checklists registered for your campus
                    node.</li>
            </ul>
            <div
                style="margin-top: auto; padding: 0.85rem; border-radius: 12px; background: var(--primary-glow); border: 1px solid var(--border-color); font-size: 0.8rem; color: var(--text-secondary);">
                <i class="fa-solid fa-lightbulb" style="color: var(--warning)"></i>
                Excel and spreadsheet software can directly read standard CSV exports.
            </div>
        </div>
    </div>

    <script>
        function toggleEntitySelects() {
            const type = document.getElementById('report_type').value;
            const teacherGrp = document.getElementById('teacher-group');
            const adminGrp = document.getElementById('admin-group');
            const campusGrp = document.getElementById('campus-group');

            // Reset inputs
            document.getElementById('teacher_id').selectedIndex = 0;
            document.getElementById('admin_id').selectedIndex = 0;
            document.getElementById('campus_id').selectedIndex = 0;

            if (type === 'teacher') {
                teacherGrp.style.display = 'flex';
                adminGrp.style.display = 'none';
                campusGrp.style.display = 'none';
            } else if (type === 'admin') {
                teacherGrp.style.display = 'none';
                adminGrp.style.display = 'flex';
                campusGrp.style.display = 'none';
            } else {
                teacherGrp.style.display = 'none';
                adminGrp.style.display = 'none';
                campusGrp.style.display = 'flex';
            }
        }
    </script>
@endsection