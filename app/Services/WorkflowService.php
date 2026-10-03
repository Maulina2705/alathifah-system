<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Deadline;
use App\Models\ReportApproval;
use App\Models\ReportReview;
use App\Models\ReportSubmission;
use App\Models\StudentAssessment;
use App\Models\User;
use Exception;

class WorkflowService
{
    /**
     * Guru submits assessment.
     */
    public function submit(StudentAssessment $assessment, User $user, ?string $notes = null): void
    {
        // 1. Verify that teacher has verified their profile
        $profile = $user->teacherProfile;
        if ($user->isGuru() && (! $profile || ! $profile->is_verified)) {
            throw new Exception('Anda belum melakukan Verifikasi Biodata Guru. Silakan verifikasi biodata Anda terlebih dahulu di menu Profil/Verifikasi Biodata.');
        }

        // 2. Check deadline
        $deadline = Deadline::where('academic_year_id', $assessment->academic_year_id)
            ->where('semester_id', $assessment->semester_id)
            ->where(function ($q) use ($assessment) {
                $q->where('level', 'SEMUA')->orWhere('level', $assessment->student->level);
            })
            ->where('is_active', true)
            ->first();

        if ($deadline && $deadline->isOverdue() && ! $user->isSuperAdmin()) {
            throw new Exception('Batas waktu (deadline) pengisian raport telah lewat pada '.$deadline->deadline_at->format('d/m/Y H:i').' WIB. Hubungi Administrator untuk perpanjangan waktu.');
        }

        $prevStatus = $assessment->status;
        $assessment->update(['status' => 'SUBMITTED']);

        ReportSubmission::create([
            'student_assessment_id' => $assessment->id,
            'submitted_by_user_id' => $user->id,
            'notes' => $notes,
            'submitted_at' => now(),
        ]);

        AuditLog::log(
            'SUBMIT_REPORT',
            "Raport siswa {$assessment->student->name} disubmit oleh {$user->name}. Status: {$prevStatus} -> SUBMITTED",
            $assessment->student_id,
            ['status' => $prevStatus],
            ['status' => 'SUBMITTED']
        );
    }

    /**
     * Wali Kelas or Admin reviews assessment.
     */
    public function review(StudentAssessment $assessment, User $user, string $status, ?string $notes = null): void
    {
        $prevStatus = $assessment->status;
        $newStatus = ($status === 'REJECTED') ? 'DRAFT' : 'REVIEWED';

        $assessment->update(['status' => $newStatus]);

        ReportReview::create([
            'student_assessment_id' => $assessment->id,
            'reviewed_by_user_id' => $user->id,
            'status' => $status,
            'notes' => $notes,
            'reviewed_at' => now(),
        ]);

        AuditLog::log(
            $status === 'REJECTED' ? 'REJECT_REPORT' : 'REVIEW_REPORT',
            "Raport siswa {$assessment->student->name} direview oleh {$user->name}. Hasil: {$status}. Status: {$prevStatus} -> {$newStatus}",
            $assessment->student_id,
            ['status' => $prevStatus],
            ['status' => $newStatus, 'notes' => $notes]
        );
    }

    /**
     * Kepala Sekolah or Admin approves assessment.
     */
    public function approve(StudentAssessment $assessment, User $user, string $status, ?string $notes = null): void
    {
        $prevStatus = $assessment->status;
        $newStatus = match ($status) {
            'REVISION_REQUESTED' => 'DRAFT',
            'LOCKED' => 'LOCKED',
            default => 'APPROVED',
        };

        $assessment->update(['status' => $newStatus]);

        ReportApproval::create([
            'student_assessment_id' => $assessment->id,
            'approved_by_user_id' => $user->id,
            'status' => $status,
            'notes' => $notes,
            'approved_at' => now(),
        ]);

        AuditLog::log(
            'APPROVE_REPORT',
            "Raport siswa {$assessment->student->name} diproses oleh {$user->name}. Tindakan: {$status}. Status: {$prevStatus} -> {$newStatus}",
            $assessment->student_id,
            ['status' => $prevStatus],
            ['status' => $newStatus, 'notes' => $notes]
        );
    }

    /**
     * Lock assessment (Admin).
     */
    public function lock(StudentAssessment $assessment, User $user): void
    {
        $prevStatus = $assessment->status;
        $assessment->update(['status' => 'LOCKED']);

        AuditLog::log(
            'LOCK_REPORT',
            "Raport siswa {$assessment->student->name} dikunci (LOCKED) oleh {$user->name}",
            $assessment->student_id,
            ['status' => $prevStatus],
            ['status' => 'LOCKED']
        );
    }

    /**
     * Unlock assessment / Request Revision (Admin).
     */
    public function unlock(StudentAssessment $assessment, User $user, ?string $reason = null): void
    {
        $prevStatus = $assessment->status;
        $assessment->update(['status' => 'DRAFT']);

        AuditLog::log(
            'UNLOCK_REPORT',
            "Raport siswa {$assessment->student->name} dibuka kembali (UNLOCK -> DRAFT) oleh {$user->name}. Alasan: ".($reason ?? 'Tanpa alasan'),
            $assessment->student_id,
            ['status' => $prevStatus],
            ['status' => 'DRAFT', 'reason' => $reason]
        );
    }

    /**
     * IT / Admin updates printing progress status.
     * Statuses: DRAFT, SUBMITTED (Submitted by Teacher), QUEUED (Menunggu Antrian Cetak), PRINTING (Proses Cetak), COMPLETED (Selesai).
     */
    public function updatePrintStatus(StudentAssessment $assessment, User $user, string $status, ?string $notes = null): void
    {
        $normalizedStatus = match ($status) {
            'QUEUED', 'MENUNGGU_CETAK' => 'MENUNGGU_CETAK',
            'PRINTING', 'PROSES_CETAK' => 'PROSES_CETAK',
            'COMPLETED', 'SELESAI' => 'SELESAI',
            default => $status,
        };

        $allowed = ['DRAFT', 'SUBMITTED', 'REVIEWED', 'APPROVED', 'MENUNGGU_CETAK', 'PROSES_CETAK', 'SELESAI', 'LOCKED'];
        if (! in_array($normalizedStatus, $allowed)) {
            throw new Exception("Status cetak tidak valid: {$status}");
        }

        $prevStatus = $assessment->status;
        $assessment->update(['status' => $normalizedStatus]);

        $roleName = $user->roles->first()?->name ?? 'User';
        AuditLog::log(
            'UPDATE_PRINT_STATUS',
            "Progress cetak raport {$assessment->student->name} diubah oleh {$user->name} ({$roleName}): {$prevStatus} -> {$status}",
            $assessment->student_id,
            ['status' => $prevStatus],
            ['status' => $status, 'notes' => $notes]
        );
    }
}
