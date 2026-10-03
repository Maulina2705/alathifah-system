<?php

namespace App\Policies;

use App\Models\StudentAssessment;
use App\Models\User;

class StudentAssessmentPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function view(User $user, StudentAssessment $assessment): bool
    {
        if ($user->id === $assessment->teacher_user_id) {
            return true;
        }

        if ($user->isWaliKelas()) {
            $assignment = $assessment->student->assignments()
                ->where('academic_year_id', $assessment->academic_year_id)
                ->where('semester_id', $assessment->semester_id)
                ->first();

            return $assignment && $assignment->homeroom_user_id === $user->id;
        }

        if ($user->isKepalaSekolah()) {
            return true;
        }

        return false;
    }

    public function update(User $user, StudentAssessment $assessment): bool
    {
        if ($user->id !== $assessment->teacher_user_id) {
            return false;
        }

        return $assessment->canEdit();
    }

    public function submit(User $user, StudentAssessment $assessment): bool
    {
        return $user->id === $assessment->teacher_user_id && $assessment->isDraft();
    }

    public function review(User $user, StudentAssessment $assessment): bool
    {
        if ($assessment->status !== 'SUBMITTED') {
            return false;
        }

        if ($user->isWaliKelas()) {
            $assignment = $assessment->student->assignments()
                ->where('academic_year_id', $assessment->academic_year_id)
                ->where('semester_id', $assessment->semester_id)
                ->first();

            return $assignment && $assignment->homeroom_user_id === $user->id;
        }

        return false;
    }

    public function approve(User $user, StudentAssessment $assessment): bool
    {
        if ($assessment->status !== 'REVIEWED') {
            return false;
        }

        return $user->isKepalaSekolah();
    }

    public function lock(User $user, StudentAssessment $assessment): bool
    {
        return $user->isSuperAdmin();
    }

    public function unlock(User $user, StudentAssessment $assessment): bool
    {
        return $user->isSuperAdmin();
    }
}
