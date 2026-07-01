<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Inspection;
use App\Models\Remark;
use App\Models\ScoreSummary;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TeacherController extends Controller
{
    public function dashboard()
    {
        \Illuminate\Support\Facades\Gate::authorize('view-dashboard');
        $user = Auth::user();

        // Latest weekly/monthly scores
        $monthKey = Carbon::now()->format('Y-m');
        $weekKey = Carbon::now()->format('Y-\WW');

        $monthlySummary = ScoreSummary::where('entity_type', 'teacher')
            ->where('teacher_id', $user->id)
            ->where('period_type', 'monthly')
            ->where('period_key', $monthKey)
            ->first();

        $weeklySummary = ScoreSummary::where('entity_type', 'teacher')
            ->where('teacher_id', $user->id)
            ->where('period_type', 'weekly')
            ->where('period_key', $weekKey)
            ->first();

        $weeklyScore = $weeklySummary->average_score ?? null;
        $monthlyScore = $monthlySummary->average_score ?? null;

        // Performance trend data (last 4 months)
        $months = [];
        $scores = [];
        for ($i = 3; $i >= 0; $i--) {
            $mk = Carbon::now()->subMonths($i)->format('Y-m');
            $months[] = Carbon::now()->subMonths($i)->format('M Y');
            $s = ScoreSummary::where('entity_type', 'teacher')
                ->where('teacher_id', $user->id)
                ->where('period_type', 'monthly')
                ->where('period_key', $mk)
                ->first();
            $scores[] = $s ? round($s->average_score, 1) : 0;
        }

        // Overall average
        $overallAvg = round(Inspection::where('type', 'teacher')->where('teacher_id', $user->id)->avg('score') ?? 0, 1);

        // Recent inspections
        $recentInspections = Inspection::where('teacher_id', $user->id)
            ->with(['inspector', 'campusClass'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('portals.teacher.dashboard', compact(
            'weeklyScore', 'monthlyScore', 'overallAvg',
            'months', 'scores', 'recentInspections'
        ));
    }

    public function profile()
    {
        \Illuminate\Support\Facades\Gate::authorize('view-profile');
        $user = Auth::user()->load('campus');

        $remarks = Remark::where('teacher_id', $user->id)
            ->where('is_private', false)
            ->with('inspector')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('portals.teacher.profile', compact('user', 'remarks'));
    }

    public function scores()
    {
        \Illuminate\Support\Facades\Gate::authorize('view-scores');
        $user = Auth::user();

        $inspections = Inspection::where('teacher_id', $user->id)
            ->with(['inspector', 'campusClass'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Monthly summaries for chart
        $months = [];
        $scoreData = [];
        for ($i = 5; $i >= 0; $i--) {
            $mk = Carbon::now()->subMonths($i)->format('Y-m');
            $months[] = Carbon::now()->subMonths($i)->format('M Y');
            $s = ScoreSummary::where('entity_type', 'teacher')
                ->where('teacher_id', $user->id)
                ->where('period_type', 'monthly')
                ->where('period_key', $mk)
                ->first();
            $scoreData[] = $s ? round($s->average_score, 1) : 0;
        }

        return view('portals.teacher.scores', compact('inspections', 'months', 'scoreData'));
    }

    public function settings()
    {
        \Illuminate\Support\Facades\Gate::authorize('update-profile');
        return view('portals.teacher.settings');
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

        return back()->with('success', 'Password changed successfully.');
    }

    public function viewInspection($id)
    {
        \Illuminate\Support\Facades\Gate::authorize('view-own-inspections');
        $user = Auth::user();
        $inspection = Inspection::with(['inspector', 'teacher', 'campus', 'campusClass'])->findOrFail($id);

        if ($inspection->teacher_id !== $user->id) {
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

        return view('portals.teacher.inspection-details', compact('inspection', 'answers'));
    }
}
