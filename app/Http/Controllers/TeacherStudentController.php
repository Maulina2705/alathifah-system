<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\StudentTeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherStudentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $activeAY = AcademicYear::where('is_active', true)->first();
        $activeSem = Semester::where('is_active', true)->first();

        $selectedAY = $request->query('ay', $activeAY?->id);
        $selectedSem = $request->query('sem', $activeSem?->id);
        $search = $request->query('search');

        $query = StudentTeacherAssignment::where('teacher_user_id', $user->id)
            ->when($selectedAY, fn ($q) => $q->where('academic_year_id', $selectedAY))
            ->when($selectedSem, fn ($q) => $q->where('semester_id', $selectedSem))
            ->with([
                'student.assessments' => function ($q) use ($selectedAY, $selectedSem) {
                    $q->where('academic_year_id', $selectedAY)->where('semester_id', $selectedSem);
                },
                'homeroom.teacherProfile',
                'academicYear',
                'semester',
            ]);

        if ($search) {
            $query->whereHas('student', function ($sq) use ($search) {
                $sq->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('nisn', 'LIKE', "%{$search}%")
                    ->orWhere('nis', 'LIKE', "%{$search}%");
            });
        }

        $assignments = $query->get();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();

        return view('teacher.students.index', compact(
            'assignments',
            'academicYears',
            'semesters',
            'selectedAY',
            'selectedSem',
            'search'
        ));
    }
}
