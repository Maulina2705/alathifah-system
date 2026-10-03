<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'nik',
        'title_degree',
        'position',
        'phone',
        'photo_path',
        'level',
        'is_verified',
        'verified_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedNameWithDegreeAttribute(): string
    {
        $name = $this->user ? $this->user->name : '';
        if ($this->title_degree) {
            return $name.', '.$this->title_degree;
        }

        return $name;
    }
}
