<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LedgerTemplateExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles
{
    protected array $data = [];

    public function __construct(iterable $assessments)
    {
        $rows = [];
        foreach ($assessments as $assessment) {
            $student = $assessment->student;
            foreach ($assessment->sections as $section) {
                foreach ($section->items as $item) {
                    $rows[] = [
                        $student->name,
                        $student->nis ?? '',
                        $student->nisn ?? '',
                        $section->name,
                        $item->name,
                        $item->score ?? '', // Nilai (kosong jika belum diisi)
                    ];
                }
            }
        }
        $this->data = $rows;
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'NIS',
            'NISN',
            'Materi',
            'Indikator / Surat',
            'Nilai (1-100)',
        ];
    }

    public function array(): array
    {
        return $this->data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E3A8A'],
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
            'D' => 25,
            'E' => 30,
            'F' => 15,
        ];
    }
}
