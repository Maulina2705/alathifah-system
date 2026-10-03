<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_assessment_id',
        'approved_by_user_id',
        'status',
        'notes',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function studentAssessment(): BelongsTo
    {
        return $this->belongsTo(StudentAssessment::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function user(): BelongsTo
    {
        return $this->approver();
    }
}
