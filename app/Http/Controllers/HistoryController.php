<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\StudentAssessment;
use App\Models\StudentTeacherAssignment;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->canAccessHistory()) {
            abort(403, 'Akses ditolak. Riwayat data hanya dapat diakses oleh Super Admin dan IT.');
        }

        $tab = $request->query('tab', 'assignments');
        $ayId = $request->query('ay');
        $semId = $request->query('sem');
        $search = $request->query('search');

        $academicYears = AcademicYear::all();
        $semesters = Semester::all();

        // 1. Assignment history
        $assignmentsQuery = StudentTeacherAssignment::with(['student', 'teacher.teacherProfile', 'homeroom.teacherProfile', 'academicYear', 'semester'])
            ->when($ayId, fn ($q) => $q->where('academic_year_id', $ayId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->when($search, function ($q) use ($search) {
                $q->whereHas('student', fn ($sq) => $sq->where('name', 'LIKE', "%{$search}%")->orWhere('nisn', 'LIKE', "%{$search}%"))
                    ->orWhereHas('teacher', fn ($tq) => $tq->where('name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('homeroom', fn ($hq) => $hq->where('name', 'LIKE', "%{$search}%"));
            });

        $assignments = $assignmentsQuery->latest()->paginate(25, ['*'], 'assign_page')->withQueryString();

        // 2. Assessment workflow history
        $assessmentsQuery = StudentAssessment::with(['student', 'teacher.teacherProfile', 'academicYear', 'semester', 'latestSubmission.submitter', 'latestReview.reviewer', 'latestApproval.approver'])
            ->when($ayId, fn ($q) => $q->where('academic_year_id', $ayId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->when($search, function ($q) use ($search) {
                $q->whereHas('student', fn ($sq) => $sq->where('name', 'LIKE', "%{$search}%")->orWhere('nisn', 'LIKE', "%{$search}%"));
            });

        $assessments = $assessmentsQuery->latest()->paginate(25, ['*'], 'assess_page')->withQueryString();

        return view('admin.history.index', compact('assignments', 'assessments', 'academicYears', 'semesters', 'tab', 'ayId', 'semId', 'search'));
    }
}
