<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deadline extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'semester_id',
        'level',
        'title',
        'deadline_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'deadline_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function isOverdue(): bool
    {
        return Carbon::now()->greaterThan($this->deadline_at);
    }

    public function isWarning(): bool
    {
        return Carbon::now()->diffInHours($this->deadline_at, false) <= 48 && ! $this->isOverdue();
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->isOverdue()) {
            return 'OVERDUE';
        }
        if ($this->isWarning()) {
            return 'WARNING';
        }

        return 'ON TIME';
    }

    public function getRemainingHumanAttribute(): string
    {
        if ($this->isOverdue()) {
            return 'Sudah lewat '.$this->deadline_at->diffForHumans();
        }

        return $this->deadline_at->diffForHumans(['parts' => 2]);
    }
}
