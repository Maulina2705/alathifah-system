<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeacherTemplateExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'Email',
            'Password (Kosongkan utk default: password)',
            'Role (GURU/WALI KELAS/KEPALA SEKOLAH)',
            'NIP',
            'NIK',
            'Gelar',
            'Jabatan',
            'Jenjang (SD/SMP/KEDUANYA)',
            'No HP',
        ];
    }

    public function array(): array
    {
        return [
            [
                'M Rizki Fauzan',
                'rizki.fauzan@alathifah.sch.id',
                'password',
                'GURU',
                '199208152019031008',
                '1471011508920001',
                'S.Pd.I',
                'Guru Tahfizh',
                'SD',
                '081234567890',
            ],
            [
                'Rima Rahmawati',
                'rima.rahmawati@alathifah.sch.id',
                'password',
                'WALI KELAS',
                '199003122018042001',
                '1471011203900002',
                'S.Pd., Gr.',
                'Wali Kelas',
                'SD',
                '081234567891',
            ],
            [
                'Era Mutia',
                'era.mutia@alathifah.sch.id',
                'password',
                'KEPALA SEKOLAH',
                '198506242010012015',
                '1471012406850003',
                'S.Pd, Gr.',
                'Kepala Sekolah',
                'SD',
                '081234567892',
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
                    'startColor' => ['rgb' => '1E3A8A'], // Navy blue color
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 30,
            'C' => 35,
            'D' => 35,
            'E' => 22,
            'F' => 22,
            'G' => 15,
            'H' => 20,
            'I' => 25,
            'J' => 18,
        ];
    }
}
