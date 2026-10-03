<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AssessmentTemplate;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAssessment;
use App\Services\AssessmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    public function edit(int $id)
    {
        $assessment = StudentAssessment::with('student')->findOrFail($id);
        $user = Auth::user();

        // Authorization check: Teacher or Super Admin or Homeroom or Principal
        if ($user->id !== $assessment->teacher_user_id && ! $user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menginput nilai siswa ini.');
        }

        return view('assessments.edit', compact('assessment'));
    }

    public function initialize(Request $request)
    {
        $studentId = $request->query('student');
        $student = Student::findOrFail($studentId);

        $activeAY = AcademicYear::where('is_active', true)->firstOrFail();
        $activeSem = Semester::where('is_active', true)->firstOrFail();

        // Check if assessment already exists
        $existing = StudentAssessment::where('student_id', $student->id)
            ->where('academic_year_id', $activeAY->id)
            ->where('semester_id', $activeSem->id)
            ->first();

        if ($existing) {
            return redirect()->route('teacher.assessments.edit', $existing->id);
        }

        $templates = AssessmentTemplate::where('is_active', true)
            ->where(function ($q) use ($student) {
                $q->where('level', 'SEMUA')->orWhere('level', $student->level);
            })
            ->get();

        return view('assessments.initialize', compact('student', 'activeAY', 'activeSem', 'templates'));
    }

    public function start(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'template_id' => 'nullable|exists:assessment_templates,id',
        ]);

        $student = Student::findOrFail($request->student_id);
        $user = Auth::user();

        $assessment = app(AssessmentService::class)->getOrCreateAssessment(
            $student,
            (int) $request->academic_year_id,
            (int) $request->semester_id,
            $user->id,
            $request->filled('template_id') ? (int) $request->template_id : null
        );

        return redirect()->route('teacher.assessments.edit', $assessment->id)
            ->with('success', "Penilaian untuk {$student->name} berhasil dimulai.");
    }
}
