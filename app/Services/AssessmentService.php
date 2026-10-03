<?php

namespace App\Services;

use App\Models\AssessmentTemplate;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\StudentAssessment;
use App\Models\StudentAssessmentItem;
use App\Models\StudentAssessmentSection;
use Illuminate\Support\Facades\DB;

class AssessmentService
{
    /**
     * Get or create student assessment for a specific term and teacher.
     */
    public function getOrCreateAssessment(
        Student $student,
        int $academicYearId,
        int $semesterId,
        int $teacherUserId,
        ?int $templateId = null
    ): StudentAssessment {
        $assessment = StudentAssessment::where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->first();

        if (! $assessment) {
            $assessment = StudentAssessment::create([
                'student_id' => $student->id,
                'academic_year_id' => $academicYearId,
                'semester_id' => $semesterId,
                'teacher_user_id' => $teacherUserId,
                'template_id' => $templateId,
                'status' => 'DRAFT',
            ]);

            // If a template is provided, populate sections & items
            if ($templateId) {
                $this->applyTemplate($assessment, $templateId);
            }
        }

        return $assessment;
    }

    /**
     * Apply blueprint template to student assessment instance.
     */
    public function applyTemplate(StudentAssessment $assessment, int $templateId): void
    {
        $template = AssessmentTemplate::with('sections.items')->findOrFail($templateId);

        DB::transaction(function () use ($assessment, $template) {
            $assessment->update(['template_id' => $template->id]);

            // Copy sections and items
            foreach ($template->sections as $secIndex => $section) {
                $assessmentSection = StudentAssessmentSection::create([
                    'student_assessment_id' => $assessment->id,
                    'name' => $section->name,
                    'order' => $section->order ?? $secIndex,
                ]);

                foreach ($section->items as $itemIndex => $item) {
                    StudentAssessmentItem::create([
                        'student_assessment_section_id' => $assessmentSection->id,
                        'name' => $item->name,
                        'order' => $item->order ?? $itemIndex,
                        'score' => null,
                    ]);
                }
            }

            AuditLog::log(
                'APPLY_TEMPLATE',
                "Menerapkan template {$template->name} ke siswa {$assessment->student->name}",
                $assessment->student_id,
                null,
                ['template' => $template->name]
            );
        });
    }

    /**
     * Update score with audit logging.
     */
    public function updateScore(StudentAssessmentItem $item, ?int $score): bool
    {
        $oldScore = $item->score;
        $cleanScore = ($score === null || $score <= 0) ? null : min(100, max(1, $score));

        if ($oldScore === $cleanScore) {
            return false;
        }

        $item->update(['score' => $cleanScore]);

        $section = $item->section;
        $assessment = $section ? $section->studentAssessment : null;

        if ($assessment) {
            AuditLog::log(
                'UPDATE_SCORE',
                "Nilai {$item->name} ({$section->name}): ".($oldScore ?? 'Belum diisi').' -> '.($cleanScore ?? 'Belum diisi'),
                $assessment->student_id,
                ['score' => $oldScore, 'item' => $item->name],
                ['score' => $cleanScore, 'item' => $item->name]
            );
        }

        return true;
    }

    /**
     * Add customized section to student assessment.
     */
    public function addSection(StudentAssessment $assessment, string $name): StudentAssessmentSection
    {
        $maxOrder = $assessment->sections()->max('order') ?? -1;
        $section = StudentAssessmentSection::create([
            'student_assessment_id' => $assessment->id,
            'name' => trim($name),
            'order' => $maxOrder + 1,
        ]);

        AuditLog::log(
            'ADD_SECTION',
            "Menambah materi '{$section->name}' untuk siswa {$assessment->student->name}",
            $assessment->student_id
        );

        return $section;
    }

    /**
     * Add customized item to student assessment section.
     */
    public function addItem(StudentAssessmentSection $section, string $name, ?int $score = null): StudentAssessmentItem
    {
        $maxOrder = $section->items()->max('order') ?? -1;
        $cleanScore = ($score === null || $score <= 0) ? null : min(100, max(1, $score));

        $item = StudentAssessmentItem::create([
            'student_assessment_section_id' => $section->id,
            'name' => trim($name),
            'order' => $maxOrder + 1,
            'score' => $cleanScore,
        ]);

        $assessment = $section->studentAssessment;
        if ($assessment) {
            AuditLog::log(
                'ADD_ITEM',
                "Menambah indikator '{$item->name}' pada materi '{$section->name}' untuk siswa {$assessment->student->name}",
                $assessment->student_id
            );
        }

        return $item;
    }

    /**
     * Remove item.
     */
    public function deleteItem(StudentAssessmentItem $item): void
    {
        $section = $item->section;
        $assessment = $section ? $section->studentAssessment : null;
        $name = $item->name;

        $item->delete();

        if ($assessment) {
            AuditLog::log(
                'DELETE_ITEM',
                "Menghapus indikator '{$name}' dari siswa {$assessment->student->name}",
                $assessment->student_id
            );
        }
    }
}
