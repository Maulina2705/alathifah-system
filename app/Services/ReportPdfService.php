<?php

namespace App\Services;

use App\Models\ReportSetting;
use App\Models\StudentAssessment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class ReportPdfService
{
    /**
     * Get report data array for rendering.
     */
    public function getReportData(StudentAssessment $assessment): array
    {
        $student = $assessment->student;
        $level = $student->level;
        $setting = ReportSetting::where('level', $level)->first();

        if (! $setting) {
            $setting = new ReportSetting([
                'level' => $level,
                'school_name' => ($level === 'SD' ? 'SD' : 'SMP').' ISLAM RIAU GLOBAL TERPADU',
                'header_title' => 'LAPORAN PERKEMBANGAN TAHFIZH AL ATHIFA',
                'city' => 'Pekanbaru',
                'principal_name' => 'Era Mutia, S.Pd, Gr.',
                'principal_nip' => '-',
                'homeroom_label' => 'Wali Kelas',
                'teacher_label' => 'Guru Tahfidz',
                'principal_label' => 'Kepala Sekolah',
                'default_report_date' => now(),
            ]);
        }

        // Filter sections and items: ONLY items with score != null and score > 0
        $filteredSections = [];
        $sections = $assessment->sections()->with(['items' => function ($q) {
            $q->whereNotNull('score')->where('score', '>', 0)->orderBy('order');
        }])->get();

        foreach ($sections as $section) {
            if ($section->items->count() > 0) {
                $filteredSections[] = $section;
            }
        }

        // Get teacher info
        $teacher = $assessment->teacher;
        $teacherProfile = $teacher ? $teacher->teacherProfile : null;
        $teacherName = $teacherProfile ? $teacherProfile->formatted_name_with_degree : ($teacher->name ?? '-');
        $teacherNip = $teacherProfile?->nip ?? '-';

        // Get homeroom info from assignment
        $assignment = $student->assignments()
            ->where('academic_year_id', $assessment->academic_year_id)
            ->where('semester_id', $assessment->semester_id)
            ->first();

        $homeroomUser = $assignment?->homeroom;
        $homeroomProfile = $homeroomUser?->teacherProfile;
        $homeroomName = $homeroomProfile ? $homeroomProfile->formatted_name_with_degree : ($homeroomUser->name ?? ($setting->homeroom_name ?? 'Rima Rahmawati, S.Pd., Gr.'));
        $homeroomNip = $homeroomProfile?->nip ?? ($setting->homeroom_nip ?? '-');

        // Report date
        $reportDate = $setting->default_report_date ?? now();

        // Image base64 encoding for 100% reliable DomPDF rendering
        $logoPath = public_path($setting->logo_path ?: 'images/logo_irgt.png');
        if (! file_exists($logoPath)) {
            $logoPath = public_path('images/logo_irgt.png');
        }
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;

        $bismillahPath = public_path('images/bismillah.png');
        $bismillahBase64 = file_exists($bismillahPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($bismillahPath)) : null;

        return [
            'assessment' => $assessment,
            'student' => $student,
            'academicYear' => $assessment->academicYear,
            'semester' => $assessment->semester,
            'setting' => $setting,
            'sections' => $filteredSections,
            'teacherName' => $teacherName,
            'teacherNip' => $teacherNip,
            'homeroomName' => $homeroomName,
            'homeroomNip' => $homeroomNip,
            'reportDate' => $reportDate,
            'logoBase64' => $logoBase64,
            'bismillahBase64' => $bismillahBase64,
        ];
    }

    /**
     * Generate HTML for report preview.
     */
    public function renderHtml(StudentAssessment $assessment): string
    {
        $data = $this->getReportData($assessment);

        return view('reports.pdf_template', $data)->render();
    }

    /**
     * Generate PDF object for a single assessment.
     */
    public function generateSinglePdf(StudentAssessment $assessment)
    {
        $data = $this->getReportData($assessment);
        $setting = $data['setting'];

        $pdf = Pdf::loadView('reports.pdf_template', $data);
        $pdf->setPaper($setting->paper_size ?? 'A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        return $pdf;
    }

    /**
     * Generate merged PDF for multiple assessments.
     */
    public function generateBulkPdf(Collection $assessments)
    {
        $reportsData = [];
        $firstSetting = null;

        foreach ($assessments as $assessment) {
            $data = $this->getReportData($assessment);
            if (! $firstSetting) {
                $firstSetting = $data['setting'];
            }
            $reportsData[] = $data;
        }

        $pdf = Pdf::loadView('reports.bulk_pdf_template', [
            'reports' => $reportsData,
            'setting' => $firstSetting,
        ]);

        $pdf->setPaper($firstSetting->paper_size ?? 'A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        return $pdf;
    }
}
