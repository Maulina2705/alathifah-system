<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    public function studentAssignments(): HasMany
    {
        return $this->hasMany(StudentTeacherAssignment::class, 'teacher_user_id');
    }

    public function homeroomAssignments(): HasMany
    {
        return $this->hasMany(StudentTeacherAssignment::class, 'homeroom_user_id');
    }

    public function studentAssessments(): HasMany
    {
        return $this->hasMany(StudentAssessment::class, 'teacher_user_id');
    }

    public function assessmentTemplates(): HasMany
    {
        return $this->hasMany(AssessmentTemplate::class, 'created_by_user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('SUPER ADMIN');
    }

    public function isIt(): bool
    {
        return $this->hasRole('IT');
    }

    public function isGuru(): bool
    {
        return $this->hasRole('GURU');
    }

    public function isWaliKelas(): bool
    {
        return $this->hasRole('WALI KELAS');
    }

    public function isKepalaSekolah(): bool
    {
        return $this->hasRole('KEPALA SEKOLAH');
    }

    public function isItOrSuperAdmin(): bool
    {
        return $this->hasRole('IT') || $this->hasRole('SUPER ADMIN');
    }

    public function canManageDeadlines(): bool
    {
        return $this->hasRole('SUPER ADMIN') || $this->hasRole('IT') || $this->hasRole('KEPALA SEKOLAH');
    }

    public function canAccessHistory(): bool
    {
        return $this->hasRole('SUPER ADMIN') || $this->hasRole('IT');
    }
}
