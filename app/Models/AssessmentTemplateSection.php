<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentTemplateSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_template_id',
        'name',
        'order',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(AssessmentTemplate::class, 'assessment_template_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AssessmentTemplateItem::class)->orderBy('order');
    }
}
