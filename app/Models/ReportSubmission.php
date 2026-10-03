<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_assessment_id',
        'submitted_by_user_id',
        'notes',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    public function studentAssessment(): BelongsTo
    {
        return $this->belongsTo(StudentAssessment::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    public function user(): BelongsTo
    {
        return $this->submitter();
    }
}
