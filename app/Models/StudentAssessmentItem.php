<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAssessmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_assessment_section_id',
        'name',
        'order',
        'score',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(StudentAssessmentSection::class, 'student_assessment_section_id');
    }

    public function hasScore(): bool
    {
        return ! is_null($this->score) && $this->score > 0;
    }
}
