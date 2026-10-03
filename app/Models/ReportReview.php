<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_assessment_id',
        'reviewed_by_user_id',
        'status',
        'notes',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function studentAssessment(): BelongsTo
    {
        return $this->belongsTo(StudentAssessment::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function user(): BelongsTo
    {
        return $this->reviewer();
    }
}
