<?php

namespace App\Http\Controllers;

use App\Exports\StudentTemplateExport;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentTeacherAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $level = $request->query('level');

        $students = Student::when($search, function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('nisn', 'LIKE', "%{$search}%")
                ->orWhere('nis', 'LIKE', "%{$search}%");
        })
            ->when($level, fn ($q) => $q->where('level', $level))
            ->with(['assignments.teacher', 'assignments.homeroom'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $activeAY = AcademicYear::where('is_active', true)->first();
        $activeSem = Semester::where('is_active', true)->first();
        $teachers = User::role('GURU')->get();
        $homerooms = User::role(['WALI KELAS', 'GURU'])->get();

        return view('admin.students.index', compact('students', 'search', 'level', 'activeAY', 'activeSem', 'teachers', 'homerooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'nis' => 'nullable|string|max:30',
            'nisn' => 'required|string|max:30|unique:students,nisn',
            'level' => 'required|in:SD,SMP',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'teacher_user_id' => 'nullable|exists:users,id',
            'homeroom_user_id' => 'nullable|exists:users,id',
        ]);

        $student = Student::create($data);

        // Assign teacher & homeroom if selected
        $activeAY = AcademicYear::where('is_active', true)->first();
        $activeSem = Semester::where('is_active', true)->first();

        if ($request->filled('teacher_user_id') && $activeAY && $activeSem) {
            StudentTeacherAssignment::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $activeAY->id,
                    'semester_id' => $activeSem->id,
                ],
                [
                    'teacher_user_id' => $request->teacher_user_id,
                    'homeroom_user_id' => $request->homeroom_user_id,
                ]
            );
        }

        AuditLog::log('CREATE_STUDENT', "Admin menambahkan data siswa {$student->name} ({$student->level})", $student->id);

        return back()->with('success', "Siswa {$student->name} berhasil ditambahkan.");
    }

    public function update(Request $request, int $id)
    {
        $student = Student::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'nis' => 'nullable|string|max:30',
            'nisn' => 'required|string|max:30|unique:students,nisn,'.$student->id,
            'level' => 'required|in:SD,SMP',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'teacher_user_id' => 'nullable|exists:users,id',
            'homeroom_user_id' => 'nullable|exists:users,id',
        ]);

        $student->update($data);

        $activeAY = AcademicYear::where('is_active', true)->first();
        $activeSem = Semester::where('is_active', true)->first();

        if ($request->filled('teacher_user_id') && $activeAY && $activeSem) {
            StudentTeacherAssignment::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $activeAY->id,
                    'semester_id' => $activeSem->id,
                ],
                [
                    'teacher_user_id' => $request->teacher_user_id,
                    'homeroom_user_id' => $request->homeroom_user_id,
                ]
            );
        }

        AuditLog::log('UPDATE_STUDENT', "Admin memperbarui data siswa {$student->name}", $student->id);

        return back()->with('success', "Data siswa {$student->name} berhasil diperbarui.");
    }

    public function destroy(int $id)
    {
        $student = Student::findOrFail($id);
        $name = $student->name;
        $student->delete();

        AuditLog::log('DELETE_STUDENT', "Admin menghapus siswa {$name}");

        return back()->with('success', "Siswa {$name} berhasil dihapus.");
    }

    public function downloadTemplate()
    {
        return Excel::download(
            new StudentTemplateExport,
            'Template_Import_Siswa.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray();

        if (count($rows) <= 1) {
            return back()->with('error', 'File Excel kosong atau hanya berisi baris judul.');
        }

        array_shift($rows); // Remove header
        $importedCount = 0;
        $updatedCount = 0;

        foreach ($rows as $row) {
            $name = mb_strtoupper(trim((string) ($row[0] ?? '')), 'UTF-8');
            $nis = trim((string) ($row[1] ?? ''));
            $nisn = trim((string) ($row[2] ?? ''));
            $level = strtoupper(trim((string) ($row[3] ?? 'SD')));
            $gender = strtoupper(trim((string) ($row[4] ?? 'L')));
            $birthPlace = trim((string) ($row[5] ?? ''));
            $birthDate = trim((string) ($row[6] ?? ''));
            $notes = trim((string) ($row[7] ?? ''));

            if ($name === '' || $nisn === '') {
                continue;
            }

            if (! in_array($level, ['SD', 'SMP'])) {
                $level = 'SD';
            }
            if (! in_array($gender, ['L', 'P'])) {
                $gender = 'L';
            }

            // Parse date if valid
            $parsedBirthDate = null;
            if ($birthDate !== '') {
                $time = strtotime($birthDate);
                if ($time !== false) {
                    $parsedBirthDate = date('Y-m-d', $time);
                }
            }

            $student = Student::where('nisn', $nisn)->first();
            if ($student) {
                $student->update([
                    'name' => $name,
                    'nis' => $nis ?: $student->nis,
                    'level' => $level,
                    'gender' => $gender,
                    'birth_place' => $birthPlace ?: $student->birth_place,
                    'birth_date' => $parsedBirthDate ?: $student->birth_date,
                    'notes' => $notes ?: $student->notes,
                ]);
                $updatedCount++;
            } else {
                Student::create([
                    'name' => $name,
                    'nis' => $nis,
                    'nisn' => $nisn,
                    'level' => $level,
                    'gender' => $gender,
                    'birth_place' => $birthPlace,
                    'birth_date' => $parsedBirthDate,
                    'notes' => $notes,
                    'is_active' => true,
                ]);
                $importedCount++;
            }
        }

        AuditLog::log('IMPORT_STUDENTS_EXCEL', "Admin mengimpor siswa via Excel: {$importedCount} baru, {$updatedCount} diperbarui.");

        return back()->with('success', "Berhasil memproses Excel: {$importedCount} siswa baru ditambahkan, {$updatedCount} data siswa diperbarui.");
    }
}
