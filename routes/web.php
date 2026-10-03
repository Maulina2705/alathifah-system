<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssessmentTemplateController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeadlineController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportSettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherStudentController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:15,1');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile & Verification
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/verify', [ProfileController::class, 'showVerification'])->name('profile.verify');
    Route::post('/profile/verify', [ProfileController::class, 'processVerification'])->name('profile.verify.submit');

    // Guru Tahfizh: My Students & Assessments (Protected for Guru & Super Admin)
    Route::prefix('teacher')->name('teacher.')->middleware('role:GURU|SUPER ADMIN')->group(function () {
        Route::get('/students', [TeacherStudentController::class, 'index'])->name('students.index');
        Route::get('/assessments/init', [AssessmentController::class, 'initialize'])->name('assessments.initialize');
        Route::post('/assessments/start', [AssessmentController::class, 'start'])->name('assessments.start');
        Route::get('/assessments/{id}/edit', [AssessmentController::class, 'edit'])->name('assessments.edit');
    });

    // Assessment Templates (Blueprint)
    Route::prefix('templates')->name('templates.')->middleware('role:GURU|SUPER ADMIN|IT')->group(function () {
        Route::get('/', [AssessmentTemplateController::class, 'index'])->name('index');
        Route::post('/', [AssessmentTemplateController::class, 'store'])->name('store');
        Route::get('/{id}', [AssessmentTemplateController::class, 'show'])->name('show');
        Route::post('/{id}/duplicate', [AssessmentTemplateController::class, 'duplicate'])->name('duplicate');
        Route::post('/{id}/sections', [AssessmentTemplateController::class, 'addSection'])->name('sections.store');
        Route::delete('/sections/{id}', [AssessmentTemplateController::class, 'deleteSection'])->name('sections.destroy');
        Route::post('/sections/{id}/items', [AssessmentTemplateController::class, 'addItem'])->name('items.store');
        Route::delete('/items/{id}', [AssessmentTemplateController::class, 'deleteItem'])->name('items.destroy');
    });

    // Reports & PDF
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/{id}/preview', [ReportController::class, 'preview'])->name('preview');
        Route::get('/{id}/pdf', [ReportController::class, 'downloadPdf'])->name('pdf');
        Route::post('/bulk-pdf', [ReportController::class, 'bulkPdf'])->name('bulk_pdf')->middleware('throttle:20,1');
        Route::get('/homeroom-bulk-pdf', [ReportController::class, 'homeroomBulkPdf'])->name('homeroom_bulk_pdf');
        Route::post('/bulk-status', [ReportController::class, 'bulkStatus'])->name('bulk_status');
        Route::post('/{id}/status', [ReportController::class, 'updateStatus'])->name('update_status');
    });

    // Excel Ledger Import & Export (Super Admin & Guru)
    Route::prefix('excel')->name('excel.')->middleware('role:GURU|SUPER ADMIN|IT')->group(function () {
        Route::get('/', [ExcelController::class, 'index'])->name('index');
        Route::get('/download-template', [ExcelController::class, 'downloadTemplate'])->name('download');
        Route::post('/preview', [ExcelController::class, 'preview'])->name('preview');
        Route::post('/confirm', [ExcelController::class, 'confirmImport'])->name('confirm');
    });

    // Wali Kelas Workflow (Protected for Wali Kelas & Super Admin)
    Route::prefix('homeroom')->name('homeroom.')->middleware('role:WALI KELAS|SUPER ADMIN')->group(function () {
        Route::get('/reviews', [WorkflowController::class, 'homeroomIndex'])->name('reviews.index');
        Route::post('/reviews/{id}', [WorkflowController::class, 'review'])->name('reviews.action');
    });

    // Kepala Sekolah Workflow (Protected for Kepala Sekolah & Super Admin)
    Route::prefix('principal')->name('principal.')->middleware('role:KEPALA SEKOLAH|SUPER ADMIN')->group(function () {
        Route::get('/approvals', [WorkflowController::class, 'principalIndex'])->name('approvals.index');
        Route::post('/approvals/{id}', [WorkflowController::class, 'approve'])->name('approvals.action');
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Deadlines (Super Admin, IT, Kepala Sekolah)
        Route::middleware('role:SUPER ADMIN|IT|KEPALA SEKOLAH')->group(function () {
            Route::resource('deadlines', DeadlineController::class)->except(['create', 'show', 'edit']);
        });

        // Core Admin Operations (Super Admin & IT only)
        Route::middleware('role:SUPER ADMIN|IT')->group(function () {
            // Students
            Route::get('students/download-template', [StudentController::class, 'downloadTemplate'])->name('students.download_template');
            Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
            Route::resource('students', StudentController::class)->except(['create', 'show', 'edit']);

            // Teachers & Staff Users
            Route::get('teachers/download-template', [TeacherController::class, 'downloadTemplate'])->name('teachers.download_template');
            Route::post('teachers/import', [TeacherController::class, 'import'])->name('teachers.import');
            Route::resource('teachers', TeacherController::class)->except(['create', 'show', 'edit']);
            Route::post('teachers/{id}/reset-password', [TeacherController::class, 'resetPassword'])->name('teachers.reset_password');

            // Academic Years & Semesters
            Route::get('/academics', [AcademicYearController::class, 'index'])->name('academics.index');
            Route::post('/academics/year', [AcademicYearController::class, 'storeYear'])->name('academics.year.store');
            Route::post('/academics/year/{id}/active', [AcademicYearController::class, 'setActiveYear'])->name('academics.year.active');
            Route::post('/academics/semester/{id}/active', [AcademicYearController::class, 'setActiveSemester'])->name('academics.semester.active');

            // Report Settings (SD & SMP)
            Route::get('/settings', [ReportSettingController::class, 'index'])->name('settings.index');
            Route::put('/settings/{level}', [ReportSettingController::class, 'update'])->name('settings.update');

            // Workflow Lock / Unlock
            Route::post('/reports/{id}/lock', [WorkflowController::class, 'lock'])->name('reports.lock');
            Route::post('/reports/{id}/unlock', [WorkflowController::class, 'unlock'])->name('reports.unlock');

            // Data History
            Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
        });

        // Audit Trail (Super Admin only)
        Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index')->middleware('role:SUPER ADMIN');
    });

    // Panduan Fitur & Hak Akses (PDF)
    Route::get('/panduan-sistem/download', [DocumentationController::class, 'downloadPdf'])->name('docs.download');
});
