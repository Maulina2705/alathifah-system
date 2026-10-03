<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentAssessmentSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_assessment_id',
        'name',
        'order',
    ];

    public function studentAssessment(): BelongsTo
    {
        return $this->belongsTo(StudentAssessment::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StudentAssessmentItem::class)->orderBy('order');
    }

    /**
     * Items that have valid non-null score (> 0) for report rendering.
     */
    public function reportItems(): HasMany
    {
        return $this->items()->whereNotNull('score')->where('score', '>', 0);
    }
}
