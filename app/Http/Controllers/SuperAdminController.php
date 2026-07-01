<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Campus;
use App\Models\CampusClass;
use App\Models\Criteria;
use App\Models\SubCriteria;
use App\Models\Question;
use App\Models\Inspection;
use App\Models\Remark;
use App\Models\ScoreSummary;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    // --- DASHBOARD ---
    public function dashboard()
    {
        \Illuminate\Support\Facades\Gate::authorize('view-monitoring');
        $kpi = [
            'campuses_count' => Campus::count(),
            'admins_count' => User::where('role', 'admin')->count(),
            'teachers_count' => User::where('role', 'teacher')->count(),
            'inspections_count' => Inspection::count(),
            'avg_teacher_score' => round(Inspection::where('type', 'teacher')->avg('score') ?? 0, 1),
            'avg_admin_score' => round(Inspection::where('type', 'admin')->avg('score') ?? 0, 1),
            'avg_campus_score' => round(Inspection::where('type', 'campus')->avg('score') ?? 0, 1),
        ];

        // Recent Audit logs & Inspections combined for "Recent Activities"
        $recent_activities = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($log) {
                return [
                    'type' => 'audit',
                    'title' => $log->action,
                    'desc' => $log->description,
                    'user' => $log->user->name ?? 'System',
                    'time' => $log->created_at->diffForHumans()
                ];
            });

        $recent_inspections = Inspection::with(['inspector', 'teacher', 'admin', 'campus'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($ins) {
                $target = '';
                if ($ins->type === 'teacher')
                    $target = $ins->teacher->name ?? 'Unknown Teacher';
                elseif ($ins->type === 'admin')
                    $target = $ins->admin->name ?? 'Unknown Admin';
                else
                    $target = $ins->campus->name ?? 'Unknown Campus';

                return [
                    'type' => 'inspection',
                    'title' => ucfirst($ins->type) . ' Inspection Logged',
                    'desc' => "Evaluated {$target} with score {$ins->score}%.",
                    'user' => $ins->inspector->name ?? 'Inspector',
                    'time' => $ins->created_at->diffForHumans()
                ];
            });

        $activities = $recent_activities->concat($recent_inspections)->sortByDesc('time')->take(8);

        // Performance Trend data for Charts (Last 4 Months)
        $months = [];
        $teacher_trends = [];
        $admin_trends = [];
        $campus_trends = [];
        for ($i = 3; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('Y-m');
            $months[] = Carbon::now()->subMonths($i)->format('M Y');
            $teacher_trends[] = round(ScoreSummary::where('entity_type', 'teacher')->where('period_type', 'monthly')->where('period_key', $month)->avg('average_score') ?? 0, 1);
            $admin_trends[] = round(ScoreSummary::where('entity_type', 'admin')->where('period_type', 'monthly')->where('period_key', $month)->avg('average_score') ?? 0, 1);
            $campus_trends[] = round(ScoreSummary::where('entity_type', 'campus')->where('period_type', 'monthly')->where('period_key', $month)->avg('average_score') ?? 0, 1);
        }

        return view('portals.super-admin.dashboard', compact('kpi', 'activities', 'months', 'teacher_trends', 'admin_trends', 'campus_trends'));
    }

    // --- CAMPUS MANAGEMENT ---
    public function campuses()
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-campuses');
        $campuses = Campus::withCount(['users', 'classes'])->get();
        return view('portals.super-admin.campuses', compact('campuses'));
    }

    public function addCampus(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-campuses');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        Campus::create($data);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'add_campus',
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Campus added successfully.');
    }

    public function editCampus(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-campuses');
        $campus = Campus::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $campus->update($data);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'edit_campus',
            'description' => "Updated campus ID {$id}: {$data['name']}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Campus updated successfully.');
    }

    public function deleteCampus(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-campuses');
        $campus = Campus::findOrFail($id);
        $name = $campus->name;
        $campus->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_campus',
            'description' => "Deleted campus: {$name} (ID {$id}).",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Campus deleted successfully.');
    }

    public function viewCampus($id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-campuses');
        $campus = Campus::with([
            'classes',
            'users' => function ($q) {
                $q->whereIn('role', ['admin', 'teacher']);
            }
        ])->findOrFail($id);

        $inspections = Inspection::where('campus_id', $id)
            ->orWhereIn('teacher_id', $campus->users->pluck('id'))
            ->orWhereIn('admin_id', $campus->users->pluck('id'))
            ->with(['inspector', 'teacher', 'admin', 'campus'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('portals.super-admin.campus-details', compact('campus', 'inspections'));
    }

    // --- ADMIN MANAGEMENT ---
    public function admins()
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-admins');
        $admins = User::where('role', 'admin')->with('campus')->get();
        $campuses = Campus::all();

        // Performance scores map
        $admin_scores = [];
        foreach ($admins as $admin) {
            $admin_scores[$admin->id] = round(Inspection::where('type', 'admin')->where('admin_id', $admin->id)->avg('score') ?? 0, 1);
        }

        return view('portals.super-admin.admins', compact('admins', 'campuses', 'admin_scores'));
    }

    public function addAdmin(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-admins');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'campus_id' => 'required|exists:campuses,id',
        ]);

        $tempPassword = \Illuminate\Support\Str::random(10);

        $admin = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($tempPassword),
            'role' => 'admin',
            'campus_id' => $data['campus_id']
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($admin->email)->send(new \App\Mail\NewAccountMail($admin, $tempPassword));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send welcome mail to admin: ' . $e->getMessage());
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'add_admin',
            'description' => "Created Campus Admin: {$data['name']} ({$data['email']}).",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Admin account created successfully. A welcome email with temporary login credentials has been sent.');
    }

    public function editAdmin(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-admins');
        $admin = User::where('role', 'admin')->findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'campus_id' => 'required|exists:campuses,id',
        ]);

        $admin->name = $data['name'];
        $admin->email = $data['email'];
        $admin->campus_id = $data['campus_id'];
        if ($data['password']) {
            $admin->password = Hash::make($data['password']);
        }
        $admin->save();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'edit_admin',
            'description' => "Modified Campus Admin ID {$id}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Admin updated successfully.');
    }

    public function deleteAdmin(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-admins');
        $admin = User::where('role', 'admin')->findOrFail($id);
        $name = $admin->name;
        $admin->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_admin',
            'description' => "Deleted Admin: {$name} (ID {$id}).",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Admin deleted successfully.');
    }

    // --- TEACHER MANAGEMENT ---
    public function teachers()
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-teachers');
        $teachers = User::where('role', 'teacher')->with('campus')->get();
        $campuses = Campus::all();

        $teacher_scores = [];
        foreach ($teachers as $t) {
            $teacher_scores[$t->id] = round(Inspection::where('type', 'teacher')->where('teacher_id', $t->id)->avg('score') ?? 0, 1);
        }

        return view('portals.super-admin.teachers', compact('teachers', 'campuses', 'teacher_scores'));
    }

    public function addTeacher(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-teachers');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'campus_id' => 'required|exists:campuses,id',
        ]);

        $tempPassword = \Illuminate\Support\Str::random(10);

        $teacher = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($tempPassword),
            'role' => 'teacher',
            'campus_id' => $data['campus_id']
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($teacher->email)->send(new \App\Mail\NewAccountMail($teacher, $tempPassword));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send welcome mail to teacher: ' . $e->getMessage());
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'add_teacher',
            'description' => "Created Teacher: {$data['name']} ({$data['email']}).",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Teacher account created successfully. A welcome email with temporary login credentials has been sent.');
    }

    public function editTeacher(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-teachers');
        $teacher = User::where('role', 'teacher')->findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'campus_id' => 'required|exists:campuses,id',
        ]);

        $teacher->name = $data['name'];
        $teacher->email = $data['email'];
        $teacher->campus_id = $data['campus_id'];
        if ($data['password']) {
            $teacher->password = Hash::make($data['password']);
        }
        $teacher->save();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'edit_teacher',
            'description' => "Updated Teacher ID {$id}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Teacher updated successfully.');
    }

    public function deleteTeacher(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-teachers');
        $teacher = User::where('role', 'teacher')->findOrFail($id);
        $name = $teacher->name;
        $teacher->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_teacher',
            'description' => "Deleted Teacher: {$name} (ID {$id}).",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Teacher deleted successfully.');
    }

    // --- CLASS MANAGEMENT ---
    public function classes()
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-classes');
        $classes = CampusClass::with('campus')->get();
        $campuses = Campus::all();
        return view('portals.super-admin.classes', compact('classes', 'campuses'));
    }

    public function addClass(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-classes');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'campus_id' => 'required|exists:campuses,id',
        ]);

        CampusClass::create($data);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'add_class',
            'description' => "Added class: {$data['name']} to campus ID {$data['campus_id']}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Class added successfully.');
    }

    public function editClass(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-classes');
        $class = CampusClass::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'campus_id' => 'required|exists:campuses,id',
        ]);

        $class->update($data);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'edit_class',
            'description' => "Updated Class ID {$id}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Class updated successfully.');
    }

    public function deleteClass(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-classes');
        $class = CampusClass::findOrFail($id);
        $name = $class->name;
        $class->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_class',
            'description' => "Deleted Class: {$name} (ID {$id}).",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Class deleted successfully.');
    }

    // --- INSPECTION CONFIGURATION ---
    public function inspectionConfig()
    {
        \Illuminate\Support\Facades\Gate::authorize('configure-inspections');
        $criteria = Criteria::with('subCriteria.questions')->get();
        return view('portals.super-admin.inspection-config', compact('criteria'));
    }

    public function addCriteria(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('configure-inspections');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:teacher,admin,campus',
            'description' => 'nullable|string'
        ]);

        Criteria::create($data);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'add_criteria',
            'description' => "Created criteria: {$data['name']} ({$data['type']}).",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Criteria added successfully.');
    }

    public function addSubCriteria(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('configure-inspections');
        $data = $request->validate([
            'criteria_id' => 'required|exists:criteria,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        SubCriteria::create($data);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'add_sub_criteria',
            'description' => "Created sub-criteria: {$data['name']}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Sub-criteria added successfully.');
    }

    public function addQuestion(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('configure-inspections');
        $data = $request->validate([
            'sub_criteria_id' => 'required|exists:sub_criteria,id',
            'question_text' => 'required|string',
            'max_score' => 'required|integer|min:1|max:100',
        ]);

        // Find max order
        $maxOrder = Question::where('sub_criteria_id', $data['sub_criteria_id'])->max('order_index') ?? 0;

        Question::create([
            'sub_criteria_id' => $data['sub_criteria_id'],
            'question_text' => $data['question_text'],
            'max_score' => $data['max_score'],
            'order_index' => $maxOrder + 1
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'add_question',
            'description' => "Added question under sub-criteria ID {$data['sub_criteria_id']}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Question added successfully.');
    }

    public function reorderQuestions(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('configure-inspections');
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:questions,id'
        ]);

        foreach ($request->order as $idx => $id) {
            Question::where('id', $id)->update(['order_index' => $idx + 1]);
        }

        return response()->json(['success' => true]);
    }

    public function deleteQuestion(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('configure-inspections');
        $q = Question::findOrFail($id);
        $q->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_question',
            'description' => "Deleted question ID {$id}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Question deleted successfully.');
    }

    public function editCriteria(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('configure-inspections');
        $criteria = Criteria::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:teacher,admin,campus',
            'description' => 'nullable|string'
        ]);

        $criteria->update($data);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'edit_criteria',
            'description' => "Updated criteria ID {$id}: {$data['name']}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Criteria updated successfully.');
    }

    public function editSubCriteria(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('configure-inspections');
        $sub = SubCriteria::findOrFail($id);
        $data = $request->validate([
            'criteria_id' => 'required|exists:criteria,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $sub->update($data);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'edit_sub_criteria',
            'description' => "Updated sub-criteria ID {$id}: {$data['name']}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Sub-criteria updated successfully.');
    }

    public function editQuestion(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('configure-inspections');
        $q = Question::findOrFail($id);
        $data = $request->validate([
            'sub_criteria_id' => 'required|exists:sub_criteria,id',
            'question_text' => 'required|string',
            'max_score' => 'required|integer|min:1|max:100',
        ]);

        $q->update($data);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'edit_question',
            'description' => "Updated question ID {$id}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Question updated successfully.');
    }

    // --- EVALUATE ADMINS ---
    public function adminInspectionForm()
    {
        \Illuminate\Support\Facades\Gate::authorize('inspect-admins');
        $admins = User::where('role', 'admin')->with('campus')->get();
        // Load admin criteria questions
        $criteria = Criteria::where('type', 'admin')->with('subCriteria.questions')->get();

        return view('portals.super-admin.admin-inspection', compact('admins', 'criteria'));
    }

    public function submitAdminInspection(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('inspect-admins');
        $request->validate([
            'admin_id' => 'required|exists:users,id',
            'scores' => 'required|array',
            'scores.*' => 'required|integer|min:0|max:100',
            'comments' => 'nullable|array',
        ]);

        $admin = User::findOrFail($request->admin_id);

        // Aggregate calculations
        $totalEarned = 0;
        $totalMax = 0;
        $rawData = [];

        foreach ($request->scores as $qId => $earned) {
            $q = Question::findOrFail($qId);
            $totalEarned += $earned;
            $totalMax += $q->max_score;

            $rawData[] = [
                'question_id' => (int) $qId,
                'score' => (int) $earned,
                'max_score' => $q->max_score,
                'comment' => $request->comments[$qId] ?? ''
            ];
        }

        $percentage = $totalMax > 0 ? round(($totalEarned / $totalMax) * 100, 2) : 0;

        // Create Inspection record
        $ins = Inspection::create([
            'inspector_id' => Auth::id(),
            'type' => 'admin',
            'admin_id' => $admin->id,
            'campus_id' => $admin->campus_id,
            'score' => $percentage,
            'raw_data' => $rawData
        ]);

        // Update Monthly Aggregated Score summaries
        $monthKey = Carbon::now()->format('Y-m');
        $summary = ScoreSummary::firstOrCreate([
            'entity_type' => 'admin',
            'admin_id' => $admin->id,
            'period_type' => 'monthly',
            'period_key' => $monthKey
        ], [
            'average_score' => $percentage,
            'inspection_count' => 0
        ]);

        $newCount = $summary->inspection_count + 1;
        $newAvg = round((($summary->average_score * $summary->inspection_count) + $percentage) / $newCount, 2);
        $summary->update([
            'average_score' => $newAvg,
            'inspection_count' => $newCount
        ]);

        // Notification
        Notification::create([
            'user_id' => $admin->id,
            'title' => 'Evaluation Submitted',
            'message' => "Super Admin has completed your performance evaluation. Score: {$percentage}%.",
            'is_read' => false
        ]);

        // Audit Trail
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'admin_inspection',
            'description' => "Submitted performance evaluation for Admin: {$admin->name}. Score: {$percentage}%.",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('super-admin.monitoring')->with('success', "Inspection logged successfully! Score: {$percentage}%.");
    }

    // --- MONITORING & INSPECTION DETAIL ---
    public function monitoring()
    {
        \Illuminate\Support\Facades\Gate::authorize('view-monitoring');
        $inspections = Inspection::with(['inspector', 'teacher', 'admin', 'campus'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('portals.super-admin.monitoring', compact('inspections'));
    }

    public function viewInspection($id)
    {
        \Illuminate\Support\Facades\Gate::authorize('view-monitoring');
        $inspection = Inspection::with(['inspector', 'teacher', 'admin', 'campus', 'campusClass'])->findOrFail($id);

        // Map raw data with question texts for rendering
        $answers = [];
        foreach ($inspection->raw_data as $ans) {
            $q = Question::with('subCriteria.criteria')->find($ans['question_id']);
            $answers[] = [
                'criteria' => $q->subCriteria->criteria->name ?? 'Criteria',
                'sub_criteria' => $q->subCriteria->name ?? 'Sub-Criteria',
                'question_text' => $q->question_text ?? 'Deleted Question',
                'score' => $ans['score'],
                'max_score' => $ans['max_score'],
                'comment' => $ans['comment'] ?? ''
            ];
        }

        return view('portals.super-admin.inspection-details', compact('inspection', 'answers'));
    }

    // --- REPORTS & EXPORT ENGINE ---
    public function reports()
    {
        \Illuminate\Support\Facades\Gate::authorize('export-reports');
        $campuses = Campus::all();
        $teachers = User::where('role', 'teacher')->get();
        $admins = User::where('role', 'admin')->get();

        return view('portals.super-admin.reports', compact('campuses', 'teachers', 'admins'));
    }

    public function exportReport(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('export-reports');
        $type = $request->query('type', 'teacher'); // teacher, admin, campus
        $entityId = $request->query('entity_id');
        $format = $request->query('format', 'csv');

        if ($format === 'pdf') {
            $query = Inspection::with(['inspector', 'teacher', 'admin', 'campus'])->where('type', $type);
            if ($entityId) {
                if ($type === 'teacher')
                    $query->where('teacher_id', $entityId);
                elseif ($type === 'admin')
                    $query->where('admin_id', $entityId);
                elseif ($type === 'campus')
                    $query->where('campus_id', $entityId);
            }

            $inspections = $query->orderBy('created_at', 'desc')->get();

            return view('portals.reports-pdf', compact('inspections', 'type'));
        }

        $filename = "tpa_report_" . $type . "_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($type, $entityId) {
            $file = fopen('php://output', 'w');

            // CSV Header Row
            fputcsv($file, ['Inspection ID', 'Inspection Type', 'Date', 'Inspector', 'Target Entity', 'Score (%)', 'Remarks/Details']);

            $query = Inspection::with(['inspector', 'teacher', 'admin', 'campus'])->where('type', $type);
            if ($entityId) {
                if ($type === 'teacher')
                    $query->where('teacher_id', $entityId);
                elseif ($type === 'admin')
                    $query->where('admin_id', $entityId);
                elseif ($type === 'campus')
                    $query->where('campus_id', $entityId);
            }

            $inspections = $query->orderBy('created_at', 'desc')->get();

            foreach ($inspections as $ins) {
                $target = '';
                if ($type === 'teacher')
                    $target = $ins->teacher->name ?? 'N/A';
                elseif ($type === 'admin')
                    $target = $ins->admin->name ?? 'N/A';
                elseif ($type === 'campus')
                    $target = $ins->campus->name ?? 'N/A';

                $inspectionType = '';
                if ($ins->type === 'teacher')
                    $inspectionType = 'Teacher Inspection';
                elseif ($ins->type === 'admin')
                    $inspectionType = 'Admin Evaluation';
                elseif ($ins->type === 'campus')
                    $inspectionType = 'Campus Inspection';

                fputcsv($file, [
                    $ins->id,
                    $inspectionType,
                    $ins->created_at->format('Y-m-d H:i'),
                    $ins->inspector->name ?? 'System',
                    $target,
                    $ins->score,
                    "Total scored percentage out of criteria questions."
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // --- SETTINGS ---
    public function settings()
    {
        \Illuminate\Support\Facades\Gate::authorize('update-profile');
        return view('portals.super-admin.settings');
    }

    public function updateProfile(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('update-profile');
        $user = Auth::user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($data);

        return back()->with('success', 'Profile settings updated successfully.');
    }

    public function changePassword(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('update-profile');
        $user = Auth::user();
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }
}
