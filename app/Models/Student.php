<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nis',
        'nisn',
        'level',
        'gender',
        'birth_place',
        'birth_date',
        'is_active',
        'notes',
    ];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? mb_strtoupper($value, 'UTF-8') : '',
            set: fn (?string $value) => $value ? mb_strtoupper(trim($value), 'UTF-8') : '',
        );
    }

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(StudentTeacherAssignment::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(StudentAssessment::class);
    }

    public function currentAssignment(?int $academicYearId = null, ?int $semesterId = null): ?StudentTeacherAssignment
    {
        $query = $this->assignments();
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        return $query->latest()->first();
    }
}
