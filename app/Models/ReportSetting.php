<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'school_name',
        'header_title',
        'school_address',
        'city',
        'logo_path',
        'principal_name',
        'principal_nip',
        'homeroom_label',
        'teacher_label',
        'principal_label',
        'default_report_date',
        'paper_size',
        'margin_top',
        'margin_bottom',
        'margin_left',
        'margin_right',
        'font_family',
    ];

    protected function casts(): array
    {
        return [
            'default_report_date' => 'date',
            'margin_top' => 'integer',
            'margin_bottom' => 'integer',
            'margin_left' => 'integer',
            'margin_right' => 'integer',
        ];
    }
}
