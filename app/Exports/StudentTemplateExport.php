<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentTemplateExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'NIS',
            'NISN',
            'Jenjang (SD/SMP)',
            'Jenis Kelamin (L/P)',
            'Tempat Lahir',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Catatan',
        ];
    }

    public function array(): array
    {
        return [
            [
                'MUHAMMAD AZZAM ALGHIFARI',
                '10471',
                '0147147650',
                'SD',
                'L',
                'Pekanbaru',
                '2014-05-12',
                'Siswa pindahan',
            ],
            [
                'FATIMAH AZ-ZAHRA',
                '10472',
                '0147147651',
                'SD',
                'P',
                'Pekanbaru',
                '2014-08-20',
                '',
            ],
            [
                'AHMAD ZAIDAN',
                '20101',
                '0147147653',
                'SMP',
                'L',
                'Pekanbaru',
                '2012-03-14',
                '',
            ],
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '059669'], // Emerald color
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 15,
            'C' => 18,
            'D' => 18,
            'E' => 20,
            'F' => 20,
            'G' => 25,
            'H' => 30,
        ];
    }
}
