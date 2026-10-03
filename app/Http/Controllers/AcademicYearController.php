<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Semester;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::with('semesters')->latest()->get();

        return view('admin.academics.index', compact('academicYears'));
    }

    public function storeYear(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20|unique:academic_years,name',
        ]);

        $ay = AcademicYear::create([
            'name' => $request->name,
            'is_active' => false,
        ]);

        // Auto create Ganjil and Genap semesters for this year
        Semester::create(['academic_year_id' => $ay->id, 'name' => 'Ganjil', 'is_active' => false]);
        Semester::create(['academic_year_id' => $ay->id, 'name' => 'Genap', 'is_active' => false]);

        AuditLog::log('CREATE_ACADEMIC_YEAR', "Admin menambah tahun pelajaran {$ay->name}");

        return back()->with('success', "Tahun pelajaran {$ay->name} berhasil ditambahkan beserta semester Ganjil & Genap.");
    }

    public function setActiveYear(int $id)
    {
        AcademicYear::query()->update(['is_active' => false]);
        $ay = AcademicYear::findOrFail($id);
        $ay->update(['is_active' => true]);

        AuditLog::log('SET_ACTIVE_YEAR', "Admin mengaktifkan tahun pelajaran {$ay->name}");

        return back()->with('success', "Tahun pelajaran {$ay->name} sekarang berstatus AKTIF.");
    }

    public function setActiveSemester(int $id)
    {
        Semester::query()->update(['is_active' => false]);
        $sem = Semester::findOrFail($id);
        $sem->update(['is_active' => true]);

        // Also make sure its academic year is active
        AcademicYear::query()->update(['is_active' => false]);
        $sem->academicYear->update(['is_active' => true]);

        AuditLog::log('SET_ACTIVE_SEMESTER', "Admin mengaktifkan semester {$sem->name} (TP {$sem->academicYear->name})");

        return back()->with('success', "Semester {$sem->name} (TP {$sem->academicYear->name}) sekarang berstatus AKTIF.");
    }
}
