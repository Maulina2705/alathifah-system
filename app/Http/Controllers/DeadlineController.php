<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Deadline;
use App\Models\Semester;
use Illuminate\Http\Request;

class DeadlineController extends Controller
{
    public function index()
    {
        if (! auth()->user()->canManageDeadlines()) {
            abort(403, 'Akses ditolak. Pengaturan batas waktu hanya dapat diakses oleh Super Admin, IT, dan Kepala Sekolah.');
        }

        $deadlines = Deadline::with(['academicYear', 'semester'])->latest()->get();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();

        return view('admin.deadlines.index', compact('deadlines', 'academicYears', 'semesters'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->canManageDeadlines()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'level' => 'required|in:SD,SMP,SEMUA',
            'title' => 'required|string|max:150',
            'deadline_at' => 'required|date',
        ]);

        $deadline = Deadline::create([
            'academic_year_id' => $request->academic_year_id,
            'semester_id' => $request->semester_id,
            'level' => $request->level,
            'title' => $request->title,
            'deadline_at' => $request->deadline_at,
            'is_active' => true,
        ]);

        $roleName = auth()->user()->roles->first()?->name ?? 'User';
        AuditLog::log('CREATE_DEADLINE', "{$roleName} (".auth()->user()->name.") membuat batas waktu input raport: {$deadline->title} ({$deadline->deadline_at})");

        return back()->with('success', 'Batas waktu input raport berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        if (! auth()->user()->canManageDeadlines()) {
            abort(403, 'Akses ditolak.');
        }

        $deadline = Deadline::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:150',
            'academic_year_id' => 'sometimes|exists:academic_years,id',
            'semester_id' => 'sometimes|exists:semesters,id',
            'level' => 'sometimes|in:SEMUA,SD,SMP',
            'deadline_at' => 'required|date',
            'is_active' => 'required|boolean',
        ]);

        $deadline->update([
            'title' => $request->title,
            'academic_year_id' => $request->input('academic_year_id', $deadline->academic_year_id),
            'semester_id' => $request->input('semester_id', $deadline->semester_id),
            'level' => $request->input('level', $deadline->level),
            'deadline_at' => $request->deadline_at,
            'is_active' => (bool) $request->is_active,
        ]);

        $roleName = auth()->user()->roles->first()?->name ?? 'User';
        AuditLog::log('UPDATE_DEADLINE', "{$roleName} (".auth()->user()->name.") mengubah batas waktu: {$deadline->title} ({$deadline->deadline_at})");

        return back()->with('success', 'Batas waktu berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        if (! auth()->user()->canManageDeadlines()) {
            abort(403, 'Akses ditolak.');
        }

        $deadline = Deadline::findOrFail($id);
        $title = $deadline->title;
        $deadline->delete();

        $roleName = auth()->user()->roles->first()?->name ?? 'User';
        AuditLog::log('DELETE_DEADLINE', "{$roleName} (".auth()->user()->name.") menghapus batas waktu: {$title}");

        return back()->with('success', 'Batas waktu berhasil dihapus.');
    }
}
