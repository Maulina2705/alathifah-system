<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\StudentAssessment;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkflowController extends Controller
{
    public function __construct(
        protected WorkflowService $workflowService
    ) {}

    public function homeroomIndex(Request $request)
    {
        $user = Auth::user();
        $activeAY = AcademicYear::where('is_active', true)->first();
        $activeSem = Semester::where('is_active', true)->first();

        $query = StudentAssessment::with(['student', 'teacher.teacherProfile', 'academicYear', 'semester'])
            ->where('status', 'SUBMITTED')
            ->when($activeAY, fn ($q) => $q->where('academic_year_id', $activeAY->id))
            ->when($activeSem, fn ($q) => $q->where('semester_id', $activeSem->id));

        if (! $user->isSuperAdmin()) {
            $query->whereHas('student.assignments', function ($sq) use ($user, $activeAY, $activeSem) {
                $sq->where('homeroom_user_id', $user->id)
                    ->when($activeAY, fn ($q) => $q->where('academic_year_id', $activeAY->id))
                    ->when($activeSem, fn ($q) => $q->where('semester_id', $activeSem->id));
            });
        }

        $assessments = $query->latest()->paginate(15);

        return view('workflow.homeroom_reviews', compact('assessments', 'activeAY', 'activeSem'));
    }

    public function review(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:REVIEWED,REJECTED',
            'notes' => 'nullable|string|max:500',
        ]);

        $assessment = StudentAssessment::findOrFail($id);
        $user = Auth::user();

        $this->workflowService->review($assessment, $user, $request->status, $request->notes);

        $msg = $request->status === 'REVIEWED'
            ? "Raport {$assessment->student->name} berhasil disetujui (REVIEWED)."
            : "Raport {$assessment->student->name} dikembalikan ke Guru (REJECTED) untuk revisi.";

        return back()->with('success', $msg);
    }

    public function principalIndex(Request $request)
    {
        $activeAY = AcademicYear::where('is_active', true)->first();
        $activeSem = Semester::where('is_active', true)->first();

        $assessments = StudentAssessment::with(['student', 'teacher.teacherProfile', 'academicYear', 'semester'])
            ->where('status', 'REVIEWED')
            ->when($activeAY, fn ($q) => $q->where('academic_year_id', $activeAY->id))
            ->when($activeSem, fn ($q) => $q->where('semester_id', $activeSem->id))
            ->latest()
            ->paginate(15);

        return view('workflow.principal_approvals', compact('assessments', 'activeAY', 'activeSem'));
    }

    public function approve(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:APPROVED,REVISION_REQUESTED',
            'notes' => 'nullable|string|max:500',
        ]);

        $assessment = StudentAssessment::findOrFail($id);
        $user = Auth::user();

        $this->workflowService->approve($assessment, $user, $request->status, $request->notes);

        $msg = $request->status === 'APPROVED'
            ? "Raport {$assessment->student->name} berhasil disetujui akhir (APPROVED)."
            : "Raport {$assessment->student->name} diminta revisi kembali.";

        return back()->with('success', $msg);
    }

    public function lock(int $id)
    {
        $assessment = StudentAssessment::findOrFail($id);
        $this->workflowService->lock($assessment, Auth::user());

        return back()->with('success', "Raport {$assessment->student->name} telah dikunci (LOCKED).");
    }

    public function unlock(Request $request, int $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $assessment = StudentAssessment::findOrFail($id);
        $this->workflowService->unlock($assessment, Auth::user(), $request->reason);

        return back()->with('success', "Raport {$assessment->student->name} telah dibuka kembali ke status DRAFT.");
    }
}
