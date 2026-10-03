<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Deadline;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAssessment;
use App\Models\StudentTeacherAssignment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $activeAY = AcademicYear::where('is_active', true)->first();
        $activeSem = Semester::where('is_active', true)->first();
        $deadline = Deadline::where('is_active', true)->latest()->first();

        $ayId = $activeAY?->id;
        $semId = $activeSem?->id;

        // Level determination for Kepsek
        $userLevel = $user->teacherProfile?->level;
        $isScopedKepsek = $user->isKepalaSekolah() && ! $user->isSuperAdmin() && ! $user->isIt();

        // Statistics for Super Admin & IT
        $totalTeachers = User::role('GURU')->count();
        $totalStudents = Student::where('is_active', true)
            ->when($isScopedKepsek && $userLevel, fn ($q) => $q->where('level', $userLevel))
            ->count();

        $assessmentQuery = StudentAssessment::where('academic_year_id', $ayId)
            ->where('semester_id', $semId)
            ->when($isScopedKepsek && $userLevel, function ($q) use ($userLevel) {
                $q->whereHas('student', fn ($sq) => $sq->where('level', $userLevel));
            });

        $statusCounts = [
            'DRAFT' => (clone $assessmentQuery)->where('status', 'DRAFT')->count(),
            'SUBMITTED' => (clone $assessmentQuery)->where('status', 'SUBMITTED')->count(),
            'REVIEWED' => (clone $assessmentQuery)->where('status', 'REVIEWED')->count(),
            'APPROVED' => (clone $assessmentQuery)->where('status', 'APPROVED')->count(),
            'MENUNGGU_CETAK' => (clone $assessmentQuery)->where('status', 'MENUNGGU_CETAK')->count(),
            'PROSES_CETAK' => (clone $assessmentQuery)->where('status', 'PROSES_CETAK')->count(),
            'SELESAI' => (clone $assessmentQuery)->where('status', 'SELESAI')->count(),
            'LOCKED' => (clone $assessmentQuery)->where('status', 'LOCKED')->count(),
        ];

        // Level Progress Calculation (SD & SMP)
        $calcLevelProgress = function (string $level) use ($ayId, $semId) {
            $assessments = StudentAssessment::where('academic_year_id', $ayId)
                ->where('semester_id', $semId)
                ->whereHas('student', fn ($q) => $q->where('level', $level))
                ->get();

            if ($assessments->isEmpty()) {
                return 0;
            }

            $totalPct = 0;
            foreach ($assessments as $assess) {
                $totalPct += $assess->progress_percentage;
            }

            return (int) round($totalPct / $assessments->count());
        };

        $sdProgress = $calcLevelProgress('SD');
        $smpProgress = $calcLevelProgress('SMP');

        // Teachers who have unsubmitted draft reports
        $teachersPending = [];
        if ($user->isSuperAdmin() || $user->isIt()) {
            $draftAssessments = StudentAssessment::where('academic_year_id', $ayId)
                ->where('semester_id', $semId)
                ->where('status', 'DRAFT')
                ->with(['teacher', 'student'])
                ->get()
                ->groupBy('teacher_user_id');

            foreach ($draftAssessments as $teacherId => $list) {
                $teacherObj = User::find($teacherId);
                if ($teacherObj) {
                    $teachersPending[] = [
                        'teacher' => $teacherObj,
                        'pending_count' => $list->count(),
                    ];
                }
            }
        }

        // Recent Audit Logs (Hanya Super Admin)
        $recentLogs = $user->isSuperAdmin() ? AuditLog::with('user', 'student')->latest()->take(7)->get() : collect();

        // Data for Guru
        $myAssignments = [];
        $guruStats = [
            'total' => 0,
            'completed' => 0,
            'incomplete' => 0,
            'submitted' => 0,
            'draft' => 0,
        ];

        if ($user->isGuru() || $user->isSuperAdmin()) {
            $assignments = StudentTeacherAssignment::where('teacher_user_id', $user->id)
                ->when($ayId, fn ($q) => $q->where('academic_year_id', $ayId))
                ->when($semId, fn ($q) => $q->where('semester_id', $semId))
                ->with(['student.assessments' => function ($q) use ($ayId, $semId) {
                    $q->where('academic_year_id', $ayId)->where('semester_id', $semId);
                }])
                ->get();

            $myAssignments = $assignments;
            $guruStats['total'] = $assignments->count();

            foreach ($assignments as $assign) {
                $assessment = $assign->student->assessments->first();
                if ($assessment) {
                    if ($assessment->progress_percentage === 100) {
                        $guruStats['completed']++;
                    } else {
                        $guruStats['incomplete']++;
                    }
                    if ($assessment->isDraft()) {
                        $guruStats['draft']++;
                    } else {
                        $guruStats['submitted']++;
                    }
                } else {
                    $guruStats['incomplete']++;
                    $guruStats['draft']++;
                }
            }
        }

        // Data for Wali Kelas
        $homeroomStudents = collect();
        $homeroomPendingCount = 0;
        if ($user->isWaliKelas() || $user->isSuperAdmin()) {
            $homeroomPendingCount = StudentAssessment::where('academic_year_id', $ayId)
                ->where('semester_id', $semId)
                ->where('status', 'SUBMITTED')
                ->when(! $user->isSuperAdmin(), function ($q) use ($user, $ayId, $semId) {
                    $q->whereHas('student.assignments', function ($sq) use ($user, $ayId, $semId) {
                        $sq->where('homeroom_user_id', $user->id)
                            ->where('academic_year_id', $ayId)
                            ->where('semester_id', $semId);
                    });
                })
                ->count();

            if ($user->isWaliKelas()) {
                $homeroomStudents = Student::whereHas('assignments', function ($q) use ($user, $ayId, $semId) {
                    $q->where('homeroom_user_id', $user->id)
                        ->where('academic_year_id', $ayId)
                        ->where('semester_id', $semId);
                })
                    ->with(['assessments' => function ($q) use ($ayId, $semId) {
                        $q->where('academic_year_id', $ayId)->where('semester_id', $semId);
                    }, 'assignments.teacher'])
                    ->get();
            }
        }

        // Data for Kepala Sekolah
        $principalPendingCount = 0;
        if ($user->isKepalaSekolah() || $user->isSuperAdmin()) {
            $principalPendingCount = StudentAssessment::where('academic_year_id', $ayId)
                ->where('semester_id', $semId)
                ->where('status', 'REVIEWED')
                ->when($isScopedKepsek && $userLevel, function ($q) use ($userLevel) {
                    $q->whereHas('student', fn ($sq) => $sq->where('level', $userLevel));
                })
                ->count();
        }

        return view('dashboard.index', compact(
            'user',
            'activeAY',
            'activeSem',
            'deadline',
            'totalTeachers',
            'totalStudents',
            'statusCounts',
            'sdProgress',
            'smpProgress',
            'teachersPending',
            'recentLogs',
            'myAssignments',
            'guruStats',
            'homeroomStudents',
            'homeroomPendingCount',
            'principalPendingCount',
            'userLevel',
            'isScopedKepsek'
        ));
    }
}
