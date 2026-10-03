<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentTemplateItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_template_section_id',
        'name',
        'order',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(AssessmentTemplateSection::class, 'assessment_template_section_id');
    }
}
