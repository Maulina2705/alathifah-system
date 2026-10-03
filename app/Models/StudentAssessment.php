<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StudentAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'semester_id',
        'teacher_user_id',
        'template_id',
        'status',
        'notes',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_user_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(AssessmentTemplate::class, 'template_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(StudentAssessmentSection::class)->orderBy('order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ReportSubmission::class)->latest();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ReportReview::class)->latest();
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ReportApproval::class)->latest();
    }

    public function latestSubmission(): HasOne
    {
        return $this->hasOne(ReportSubmission::class)->latestOfMany();
    }

    public function latestReview(): HasOne
    {
        return $this->hasOne(ReportReview::class)->latestOfMany();
    }

    public function latestApproval(): HasOne
    {
        return $this->hasOne(ReportApproval::class)->latestOfMany();
    }

    public function allItems()
    {
        return StudentAssessmentItem::whereIn('student_assessment_section_id', $this->sections()->pluck('id'));
    }

    public function getTotalItemsCountAttribute(): int
    {
        return $this->allItems()->count();
    }

    public function getFilledItemsCountAttribute(): int
    {
        return $this->allItems()->whereNotNull('score')->where('score', '>', 0)->count();
    }

    public function getUnfilledItemsCountAttribute(): int
    {
        return $this->allItems()->where(function ($q) {
            $q->whereNull('score')->orWhere('score', '<=', 0);
        })->count();
    }

    public function getProgressPercentageAttribute(): int
    {
        $total = $this->total_items_count;
        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->filled_items_count / $total) * 100);
    }

    public function isDraft(): bool
    {
        return $this->status === 'DRAFT';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'SUBMITTED';
    }

    public function isReviewed(): bool
    {
        return $this->status === 'REVIEWED';
    }

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function isLocked(): bool
    {
        return $this->status === 'LOCKED';
    }

    public function canEdit(): bool
    {
        return in_array($this->status, ['DRAFT']);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'DRAFT' => 'Draft',
            'SUBMITTED' => 'Submitted by Teacher',
            'QUEUED', 'MENUNGGU_CETAK' => 'Menunggu Antrian Cetak',
            'PRINTING', 'PROSES_CETAK' => 'Proses Cetak',
            'COMPLETED', 'SELESAI' => 'Selesai',
            'REVIEWED' => 'Reviewed (Wali Kelas)',
            'APPROVED' => 'Approved (Kepsek)',
            'LOCKED' => 'Selesai (Locked)',
            default => $this->status,
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'DRAFT' => 'bg-slate-100 text-slate-700 border-slate-200',
            'SUBMITTED' => 'bg-blue-100 text-blue-800 border-blue-200',
            'QUEUED', 'MENUNGGU_CETAK' => 'bg-amber-100 text-amber-800 border-amber-200',
            'PRINTING', 'PROSES_CETAK' => 'bg-purple-100 text-purple-800 border-purple-200',
            'COMPLETED', 'SELESAI', 'LOCKED', 'APPROVED' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'REVIEWED' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
