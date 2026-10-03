<?php

namespace App\Http\Controllers;

use App\Exports\LedgerTemplateExport;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\StudentAssessment;
use App\Services\ExcelAssessmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function __construct(
        protected ExcelAssessmentService $excelService
    ) {}

    public function index()
    {
        $user = Auth::user();
        $activeAY = AcademicYear::where('is_active', true)->first();
        $activeSem = Semester::where('is_active', true)->first();

        return view('excel.index', compact('user', 'activeAY', 'activeSem'));
    }

    public function downloadTemplate(Request $request)
    {
        $user = Auth::user();
        $activeAY = AcademicYear::where('is_active', true)->firstOrFail();
        $activeSem = Semester::where('is_active', true)->firstOrFail();

        $query = StudentAssessment::where('academic_year_id', $activeAY->id)
            ->where('semester_id', $activeSem->id)
            ->with(['student', 'sections.items']);

        if ($user->isGuru() && ! $user->isSuperAdmin()) {
            $query->where('teacher_user_id', $user->id);
        }

        $assessments = $query->get();

        if ($assessments->isEmpty()) {
            return back()->with('error', 'Belum ada siswa atau data penilaian yang dapat diekspor ke template.');
        }

        $filename = "Template_Ledger_Tahfizh_{$activeAY->name}_{$activeSem->name}.xlsx";
        $filename = str_replace('/', '-', $filename);

        return Excel::download(new LedgerTemplateExport($assessments), $filename);
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $activeAY = AcademicYear::where('is_active', true)->firstOrFail();
        $activeSem = Semester::where('is_active', true)->firstOrFail();
        $user = Auth::user();

        $path = $request->file('file')->getRealPath();

        $result = $this->excelService->previewImport(
            $path,
            $activeAY->id,
            $activeSem->id,
            $user->id
        );

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        // Store importable data temporarily in session
        session(['pending_excel_import' => $result['importable_data']]);

        return view('excel.preview', [
            'totalRows' => $result['total_rows'],
            'validCount' => $result['valid_count'],
            'invalidCount' => $result['invalid_count'],
            'warningCount' => $result['warning_count'],
            'details' => $result['details'],
            'activeAY' => $activeAY,
            'activeSem' => $activeSem,
        ]);
    }

    public function confirmImport(Request $request)
    {
        $importableData = session('pending_excel_import');

        if (! $importableData || count($importableData) === 0) {
            return redirect()->route('excel.index')->with('error', 'Tidak ada data valid yang dapat diimpor.');
        }

        $activeAY = AcademicYear::where('is_active', true)->firstOrFail();
        $activeSem = Semester::where('is_active', true)->firstOrFail();
        $user = Auth::user();

        $count = $this->excelService->executeImport(
            $importableData,
            $activeAY->id,
            $activeSem->id,
            $user->id
        );

        session()->forget('pending_excel_import');

        return redirect()->route('teacher.students.index')
            ->with('success', "Berhasil mengimpor {$count} nilai siswa dari file Excel.");
    }
}
