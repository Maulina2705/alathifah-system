<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'description',
        'created_by_user_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(AssessmentTemplateSection::class)->orderBy('order');
    }

    public function studentAssessments(): HasMany
    {
        return $this->hasMany(StudentAssessment::class, 'template_id');
    }
}
