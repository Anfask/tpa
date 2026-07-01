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

class AdminController extends Controller
{
    // --- DASHBOARD ---
    public function dashboard()
    {
        \Illuminate\Support\Facades\Gate::authorize('view-dashboard');
        $user = Auth::user();
        $campusId = $user->campus_id;

        if (!$campusId) {
            return redirect()->route('login')->with('error', 'Administrator has no assigned campus.');
        }

        $teachersCount = User::where('role', 'teacher')->where('campus_id', $campusId)->count();
        
        // Campus Average Score
        $avgCampusScore = round(Inspection::where('type', 'campus')->where('campus_id', $campusId)->avg('score') ?? 0, 1);
        
        // Personal Score from Super Admin evaluation
        $personalScore = round(Inspection::where('type', 'admin')->where('admin_id', $user->id)->avg('score') ?? 0, 1);

        // Recent evaluations completed by this Admin
        $recentInspections = Inspection::where('inspector_id', $user->id)
            ->with(['teacher', 'campusClass'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Calculate pending inspections: Teachers in this campus who haven't been evaluated this month
        $monthKey = Carbon::now()->format('Y-m');
        $evaluatedTeacherIds = Inspection::where('type', 'teacher')
            ->where('inspector_id', $user->id)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->pluck('teacher_id')
            ->toArray();
        
        $pendingInspectionsCount = User::where('role', 'teacher')
            ->where('campus_id', $campusId)
            ->whereNotIn('id', $evaluatedTeacherIds)
            ->count();

        return view('portals.admin.dashboard', compact(
            'teachersCount', 
            'pendingInspectionsCount', 
            'avgCampusScore', 
            'personalScore', 
            'recentInspections'
        ));
    }

    // --- TEACHER MANAGEMENT (Restricted to Campus) ---
    public function teachers()
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-campus-teachers');
        $user = Auth::user();
        $teachers = User::where('role', 'teacher')->where('campus_id', $user->campus_id)->get();
        
        $teacher_scores = [];
        foreach ($teachers as $t) {
            $teacher_scores[$t->id] = round(Inspection::where('type', 'teacher')->where('teacher_id', $t->id)->avg('score') ?? 0, 1);
        }

        return view('portals.admin.teachers', compact('teachers', 'teacher_scores'));
    }

    public function addTeacher(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-campus-teachers');
        $user = Auth::user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $tempPassword = \Illuminate\Support\Str::random(10);

        $teacher = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($tempPassword),
            'role' => 'teacher',
            'campus_id' => $user->campus_id // Enforced campus boundary
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($teacher->email)->send(new \App\Mail\NewAccountMail($teacher, $tempPassword));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send welcome mail to teacher (admin portal): ' . $e->getMessage());
        }

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'admin_add_teacher',
            'description' => "Created Teacher {$data['name']} under campus ID {$user->campus_id}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Teacher added successfully. A welcome email with login credentials has been sent.');
    }

    public function editTeacher(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-campus-teachers');
        $user = Auth::user();
        $teacher = User::where('role', 'teacher')->where('campus_id', $user->campus_id)->findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
        ]);

        $teacher->name = $data['name'];
        $teacher->email = $data['email'];
        if ($data['password']) {
            $teacher->password = Hash::make($data['password']);
        }
        $teacher->save();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'admin_edit_teacher',
            'description' => "Modified Teacher ID {$id}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Teacher updated successfully.');
    }

    public function deleteTeacher(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-campus-teachers');
        $user = Auth::user();
        $teacher = User::where('role', 'teacher')->where('campus_id', $user->campus_id)->findOrFail($id);
        $name = $teacher->name;
        $teacher->delete();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'admin_delete_teacher',
            'description' => "Deleted Teacher: {$name} (ID {$id}).",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Teacher account deleted successfully.');
    }

    public function teacherProfile($id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-campus-teachers');
        $user = Auth::user();
        $teacher = User::where('role', 'teacher')->where('campus_id', $user->campus_id)->findOrFail($id);
        
        $inspections = Inspection::where('teacher_id', $id)
            ->with(['inspector', 'campusClass'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $remarks = Remark::where('teacher_id', $id)
            ->with('inspector')
            ->orderBy('created_at', 'desc')
            ->get();

        $avgScore = round($inspections->avg('score') ?? 0, 1);

        return view('portals.admin.teacher-profile', compact('teacher', 'inspections', 'remarks', 'avgScore'));
    }

    // --- TEACHER REMARKS ---
    public function addRemark(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('add-remarks');
        $user = Auth::user();
        $data = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'content' => 'required|string',
            'is_private' => 'required|boolean'
        ]);

        // Enforce boundary check
        $teacher = User::where('role', 'teacher')->where('campus_id', $user->campus_id)->findOrFail($data['teacher_id']);

        Remark::create([
            'inspector_id' => $user->id,
            'teacher_id' => $teacher->id,
            'content' => $data['content'],
            'is_private' => $data['is_private']
        ]);

        if (!$data['is_private']) {
            Notification::create([
                'user_id' => $teacher->id,
                'title' => 'New Remark Added',
                'message' => "Admin {$user->name} has added a new feedback remark to your profile.",
                'is_read' => false
            ]);
        }

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'add_remark',
            'description' => "Added remark for Teacher: {$teacher->name} (Private: " . ($data['is_private'] ? 'Yes' : 'No') . ").",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Remark logged successfully.');
    }

    // --- TEACHER INSPECTIONS ---
    public function teacherInspectionForm()
    {
        \Illuminate\Support\Facades\Gate::authorize('inspect-teachers');
        $user = Auth::user();
        // Load teachers from assigned campus
        $teachers = User::where('role', 'teacher')->where('campus_id', $user->campus_id)->get();
        // Load classes from assigned campus
        $classes = CampusClass::where('campus_id', $user->campus_id)->get();
        // Load criteria questions of type 'teacher'
        $criteria = Criteria::where('type', 'teacher')->with('subCriteria.questions')->get();

        return view('portals.admin.teacher-inspection', compact('teachers', 'classes', 'criteria'));
    }

    public function submitTeacherInspection(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('inspect-teachers');
        $user = Auth::user();
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:classes,id',
            'scores' => 'required|array',
            'scores.*' => 'required|integer|min:0|max:100',
            'comments' => 'nullable|array',
        ]);

        $teacher = User::where('role', 'teacher')->where('campus_id', $user->campus_id)->findOrFail($request->teacher_id);
        $class = CampusClass::where('campus_id', $user->campus_id)->findOrFail($request->class_id);

        $totalEarned = 0;
        $totalMax = 0;
        $rawData = [];

        foreach ($request->scores as $qId => $earned) {
            $q = Question::findOrFail($qId);
            $totalEarned += $earned;
            $totalMax += $q->max_score;
            
            $rawData[] = [
                'question_id' => (int)$qId,
                'score' => (int)$earned,
                'max_score' => $q->max_score,
                'comment' => $request->comments[$qId] ?? ''
            ];
        }

        $percentage = $totalMax > 0 ? round(($totalEarned / $totalMax) * 100, 2) : 0;

        // Create Inspection
        Inspection::create([
            'inspector_id' => $user->id,
            'type' => 'teacher',
            'teacher_id' => $teacher->id,
            'campus_id' => $user->campus_id,
            'class_id' => $class->id,
            'score' => $percentage,
            'raw_data' => $rawData
        ]);

        // Recalculate Monthly Score Summary for Teacher
        $monthKey = Carbon::now()->format('Y-m');
        $summary = ScoreSummary::firstOrCreate([
            'entity_type' => 'teacher',
            'teacher_id' => $teacher->id,
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

        // Send Notification to Teacher
        Notification::create([
            'user_id' => $teacher->id,
            'title' => 'Inspection Form Filed',
            'message' => "Admin {$user->name} has completed your evaluation in {$class->name}. Score: {$percentage}%.",
            'is_read' => false
        ]);

        // Audit Trail
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'teacher_inspection',
            'description' => "Evaluated Teacher: {$teacher->name} in Class {$class->name}. Score: {$percentage}%.",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.teachers.profile', $teacher->id)->with('success', "Teacher evaluated successfully! Score: {$percentage}%.");
    }

    // --- CAMPUS INSPECTION ---
    public function campusInspectionForm()
    {
        \Illuminate\Support\Facades\Gate::authorize('inspect-campus');
        $user = Auth::user();
        // Load campus criteria questions
        $criteria = Criteria::where('type', 'campus')->with('subCriteria.questions')->get();
        return view('portals.admin.campus-inspection', compact('criteria'));
    }

    public function submitCampusInspection(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('inspect-campus');
        $user = Auth::user();
        $campusId = $user->campus_id;
        
        $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'required|integer|min:0|max:100',
            'comments' => 'nullable|array',
        ]);

        $totalEarned = 0;
        $totalMax = 0;
        $rawData = [];

        foreach ($request->scores as $qId => $earned) {
            $q = Question::findOrFail($qId);
            $totalEarned += $earned;
            $totalMax += $q->max_score;
            
            $rawData[] = [
                'question_id' => (int)$qId,
                'score' => (int)$earned,
                'max_score' => $q->max_score,
                'comment' => $request->comments[$qId] ?? ''
            ];
        }

        $percentage = $totalMax > 0 ? round(($totalEarned / $totalMax) * 100, 2) : 0;

        // Create Campus Inspection
        Inspection::create([
            'inspector_id' => $user->id,
            'type' => 'campus',
            'campus_id' => $campusId,
            'score' => $percentage,
            'raw_data' => $rawData
        ]);

        // Recalculate Monthly Score Summary for Campus
        $monthKey = Carbon::now()->format('Y-m');
        $summary = ScoreSummary::firstOrCreate([
            'entity_type' => 'campus',
            'campus_id' => $campusId,
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

        // Audit Trail
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'campus_inspection',
            'description' => "Submitted monthly campus safety/cleanliness inspection. Score: {$percentage}%.",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.dashboard')->with('success', "Campus inspection logged successfully! Score: {$percentage}%.");
    }

    // --- PERSONAL PERFORMANCE EVALUATION BY SUPER ADMIN ---
    public function personalPerformance()
    {
        \Illuminate\Support\Facades\Gate::authorize('view-performance');
        $user = Auth::user();
        
        $evaluations = Inspection::where('type', 'admin')
            ->where('admin_id', $user->id)
            ->with('inspector')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('portals.admin.performance', compact('evaluations'));
    }

    // --- SETTINGS ---
    public function settings()
    {
        \Illuminate\Support\Facades\Gate::authorize('update-profile');
        return view('portals.admin.settings');
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

        return back()->with('success', 'Profile updated successfully.');
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

        return back()->with('success', 'Password updated successfully.');
    }

    public function reports()
    {
        \Illuminate\Support\Facades\Gate::authorize('export-campus-reports');
        $user = Auth::user();
        $campusId = $user->campus_id;

        $campuses = Campus::where('id', $campusId)->get();
        $teachers = User::where('role', 'teacher')->where('campus_id', $campusId)->get();
        $admins = User::where('role', 'admin')->where('id', $user->id)->get();
        
        return view('portals.admin.reports', compact('campuses', 'teachers', 'admins'));
    }

    public function exportReport(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('export-campus-reports');
        $user = Auth::user();
        $campusId = $user->campus_id;
        if (!$campusId) {
            return back()->with('error', 'Administrator has no assigned campus.');
        }

        $type = $request->query('type', 'teacher'); // teacher, admin, campus
        $entityId = $request->query('entity_id');
        $format = $request->query('format', 'csv');

        if ($format === 'pdf') {
            $query = Inspection::with(['inspector', 'teacher', 'admin', 'campus'])
                ->where('type', $type)
                ->where('campus_id', $campusId);

            if ($entityId) {
                if ($type === 'teacher') $query->where('teacher_id', $entityId);
                elseif ($type === 'admin') $query->where('admin_id', $entityId);
                elseif ($type === 'campus') $query->where('campus_id', $entityId);
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

        $callback = function() use ($type, $entityId, $campusId) {
            $file = fopen('php://output', 'w');
            
            // CSV Header Row
            fputcsv($file, ['Inspection ID', 'Inspection Type', 'Date', 'Inspector', 'Target Entity', 'Score (%)', 'Remarks/Details']);

            $query = Inspection::with(['inspector', 'teacher', 'admin', 'campus'])
                ->where('type', $type)
                ->where('campus_id', $campusId);

            if ($entityId) {
                if ($type === 'teacher') $query->where('teacher_id', $entityId);
                elseif ($type === 'admin') $query->where('admin_id', $entityId);
                elseif ($type === 'campus') $query->where('campus_id', $entityId);
            }
            
            $inspections = $query->orderBy('created_at', 'desc')->get();

            foreach ($inspections as $ins) {
                $target = '';
                if ($type === 'teacher') $target = $ins->teacher->name ?? 'N/A';
                elseif ($type === 'admin') $target = $ins->admin->name ?? 'N/A';
                elseif ($type === 'campus') $target = $ins->campus->name ?? 'N/A';

                $inspectionType = '';
                if ($ins->type === 'teacher') $inspectionType = 'Teacher Inspection';
                elseif ($ins->type === 'admin') $inspectionType = 'Admin Evaluation';
                elseif ($ins->type === 'campus') $inspectionType = 'Campus Inspection';

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

    public function viewInspection($id)
    {
        \Illuminate\Support\Facades\Gate::authorize('view-monitoring');
        $user = Auth::user();
        $inspection = Inspection::with(['inspector', 'teacher', 'admin', 'campus', 'campusClass'])->findOrFail($id);

        // Security boundaries: Check if the admin is inspector, target admin, or if it is on their campus.
        if ($inspection->inspector_id !== $user->id && 
            $inspection->admin_id !== $user->id && 
            $inspection->campus_id !== $user->campus_id) {
            abort(403, 'Unauthorized access to this inspection.');
        }

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

        return view('portals.admin.inspection-details', compact('inspection', 'answers'));
    }
}
