<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\StudentAssessment;
use App\Models\StudentTeacherAssignment;
use App\Services\ReportPdfService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct(
        protected ReportPdfService $pdfService,
        protected WorkflowService $workflowService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $activeAY = AcademicYear::where('is_active', true)->first();
        $activeSem = Semester::where('is_active', true)->first();

        $status = $request->query('status');
        $level = $request->query('level');
        $ayId = $request->query('ay', $activeAY?->id);
        $semId = $request->query('sem', $activeSem?->id);
        $search = $request->query('search');

        $query = StudentAssessment::with(['student', 'teacher.teacherProfile', 'academicYear', 'semester'])
            ->when($ayId, fn ($q) => $q->where('academic_year_id', $ayId))
            ->when($semId, fn ($q) => $q->where('semester_id', $semId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($level, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('level', $level)));

        // Role-based filtering
        if ($user->isGuru() && ! $user->isSuperAdmin() && ! $user->isIt()) {
            $query->where('teacher_user_id', $user->id);
        } elseif ($user->isWaliKelas() && ! $user->isSuperAdmin() && ! $user->isIt() && ! $user->isKepalaSekolah()) {
            $studentIds = StudentTeacherAssignment::where('homeroom_user_id', $user->id)
                ->when($ayId, fn ($q) => $q->where('academic_year_id', $ayId))
                ->when($semId, fn ($q) => $q->where('semester_id', $semId))
                ->pluck('student_id');
            $query->whereIn('student_id', $studentIds);
        } elseif ($user->isKepalaSekolah() && ! $user->isSuperAdmin() && ! $user->isIt()) {
            $kepsekLevel = $user->teacherProfile?->level ?? 'SD';
            if (in_array($kepsekLevel, ['SD', 'SMP'])) {
                $query->whereHas('student', fn ($sq) => $sq->where('level', $kepsekLevel));
            }
        }

        if ($search) {
            $query->whereHas('student', function ($sq) use ($search) {
                $sq->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('nisn', 'LIKE', "%{$search}%");
            });
        }

        $assessments = $query->latest()->paginate(20)->withQueryString();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();

        return view('reports.index', compact(
            'assessments',
            'academicYears',
            'semesters',
            'status',
            'level',
            'ayId',
            'semId',
            'search'
        ));
    }

    public function preview(Request $request, int $id)
    {
        $assessment = StudentAssessment::with(['student', 'sections.items', 'teacher.teacherProfile', 'academicYear', 'semester'])
            ->findOrFail($id);

        $user = Auth::user();

        // Build navigation query within current user's scope
        $query = StudentAssessment::where('academic_year_id', $assessment->academic_year_id)
            ->where('semester_id', $assessment->semester_id);

        if ($user->isWaliKelas() && ! $user->isSuperAdmin() && ! $user->isIt() && ! $user->isKepalaSekolah()) {
            $studentIds = StudentTeacherAssignment::where('homeroom_user_id', $user->id)
                ->where('academic_year_id', $assessment->academic_year_id)
                ->where('semester_id', $assessment->semester_id)
                ->pluck('student_id');
            $query->whereIn('student_id', $studentIds);
        } elseif ($user->isGuru() && ! $user->isSuperAdmin() && ! $user->isIt()) {
            $query->where('teacher_user_id', $user->id);
        } elseif ($user->isKepalaSekolah() && ! $user->isSuperAdmin() && ! $user->isIt()) {
            $kepsekLevel = $user->teacherProfile?->level ?? 'SD';
            if (in_array($kepsekLevel, ['SD', 'SMP'])) {
                $query->whereHas('student', fn ($sq) => $sq->where('level', $kepsekLevel));
            }
        }

        $allIds = (clone $query)->orderBy('id')->pluck('id')->toArray();
        $currentIndex = array_search($assessment->id, $allIds);
        $prevId = ($currentIndex !== false && $currentIndex > 0) ? $allIds[$currentIndex - 1] : null;
        $nextId = ($currentIndex !== false && $currentIndex < count($allIds) - 1) ? $allIds[$currentIndex + 1] : null;
        $totalInScope = count($allIds);
        $currentPos = ($currentIndex !== false) ? ($currentIndex + 1) : 1;

        $reportHtml = $this->pdfService->renderHtml($assessment);

        return view('reports.preview_wrapper', compact(
            'assessment',
            'reportHtml',
            'prevId',
            'nextId',
            'totalInScope',
            'currentPos'
        ));
    }

    public function downloadPdf(Request $request, int $id)
    {
        $assessment = StudentAssessment::findOrFail($id);
        $pdf = $this->pdfService->generateSinglePdf($assessment);

        $safeName = str_replace(' ', '_', preg_replace('/[^A-Za-z0-9 _-]/', '', $assessment->student->name));
        $filename = "Raport_Tahfizh_{$safeName}_{$assessment->semester->name}.pdf";

        if ($request->boolean('stream')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    public function bulkPdf(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []);

        // Fallback: If no checkbox selected, gather all filtered IDs in current scope
        if (empty($selectedIds)) {
            $user = Auth::user();
            $query = StudentAssessment::query();

            if ($request->filled('ay')) {
                $query->where('academic_year_id', $request->ay);
            }
            if ($request->filled('sem')) {
                $query->where('semester_id', $request->sem);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('level')) {
                $query->whereHas('student', fn ($sq) => $sq->where('level', $request->level));
            }

            if ($user->isGuru() && ! $user->isSuperAdmin() && ! $user->isIt()) {
                $query->where('teacher_user_id', $user->id);
            } elseif ($user->isWaliKelas() && ! $user->isSuperAdmin() && ! $user->isIt()) {
                $studentIds = StudentTeacherAssignment::where('homeroom_user_id', $user->id)->pluck('student_id');
                $query->whereIn('student_id', $studentIds);
            } elseif ($user->isKepalaSekolah() && ! $user->isSuperAdmin() && ! $user->isIt()) {
                $kepsekLevel = $user->teacherProfile?->level ?? 'SD';
                $query->whereHas('student', fn ($sq) => $sq->where('level', $kepsekLevel));
            }

            $selectedIds = $query->pluck('id')->toArray();
        }

        if (empty($selectedIds)) {
            return back()->with('error', 'Tidak ada raport yang dipilih atau sesuai filter saat ini.');
        }

        $assessments = StudentAssessment::whereIn('id', $selectedIds)
            ->with(['student', 'sections.items', 'teacher.teacherProfile', 'academicYear', 'semester'])
            ->get();

        $pdf = $this->pdfService->generateBulkPdf($assessments);
        $filename = 'Bulk_Raport_Tahfizh_Al_Athifa_'.count($assessments).'_Siswa.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Download combined PDF for entire class (Wali Kelas shortcut).
     */
    public function homeroomBulkPdf(Request $request)
    {
        $user = Auth::user();
        $activeAY = AcademicYear::where('is_active', true)->first();
        $activeSem = Semester::where('is_active', true)->first();

        $ayId = $request->query('ay', $activeAY?->id);
        $semId = $request->query('sem', $activeSem?->id);

        $query = StudentAssessment::where('academic_year_id', $ayId)
            ->where('semester_id', $semId)
            ->with(['student', 'sections.items', 'teacher.teacherProfile', 'academicYear', 'semester']);

        if ($user->isWaliKelas() && ! $user->isSuperAdmin() && ! $user->isIt()) {
            $studentIds = StudentTeacherAssignment::where('homeroom_user_id', $user->id)
                ->where('academic_year_id', $ayId)
                ->where('semester_id', $semId)
                ->pluck('student_id');
            $query->whereIn('student_id', $studentIds);
        }

        $assessments = $query->get();

        if ($assessments->isEmpty()) {
            return back()->with('error', 'Belum ada data raport siswa untuk kelas Anda pada semester ini.');
        }

        $pdf = $this->pdfService->generateBulkPdf($assessments);
        $filename = 'Raport_1_Kelas_WaliKelas_'.count($assessments).'_Siswa.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Update single print progress status (IT & Super Admin).
     */
    public function updateStatus(Request $request, int $id)
    {
        $user = Auth::user();
        if (! $user->isItOrSuperAdmin()) {
            abort(403, 'Akses ditolak. Hanya IT dan Super Admin yang dapat mengubah status percetakan.');
        }

        $request->validate([
            'status' => 'required|in:DRAFT,SUBMITTED,REVIEWED,APPROVED,MENUNGGU_CETAK,PROSES_CETAK,SELESAI,QUEUED,PRINTING,COMPLETED,LOCKED',
            'notes' => 'nullable|string|max:255',
        ]);

        $assessment = StudentAssessment::findOrFail($id);
        $this->workflowService->updatePrintStatus($assessment, $user, $request->status, $request->notes);

        return back()->with('success', "Status raport {$assessment->student->name} berhasil diubah menjadi {$assessment->fresh()->status_label}.");
    }

    /**
     * Bulk update printing status (IT & Super Admin).
     */
    public function bulkStatus(Request $request)
    {
        $user = Auth::user();
        if (! $user->isItOrSuperAdmin()) {
            abort(403, 'Akses ditolak. Hanya IT dan Super Admin yang dapat mengubah status percetakan massal.');
        }

        $request->validate([
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:student_assessments,id',
            'bulk_status' => 'required|in:DRAFT,SUBMITTED,REVIEWED,APPROVED,MENUNGGU_CETAK,PROSES_CETAK,SELESAI,QUEUED,PRINTING,COMPLETED,LOCKED',
        ]);

        $assessments = StudentAssessment::whereIn('id', $request->selected_ids)->get();
        foreach ($assessments as $assess) {
            $this->workflowService->updatePrintStatus($assess, $user, $request->bulk_status);
        }

        return back()->with('success', count($assessments).' raport berhasil diupdate ke status: '.$request->bulk_status);
    }
}
