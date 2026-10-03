<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Student;
use App\Models\StudentAssessment;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelAssessmentService
{
    public function previewImport(string $filePath, int $academicYearId, int $semesterId, int $teacherUserId): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) <= 1) {
            return [
                'success' => false,
                'message' => 'File Excel kosong atau hanya memiliki header baris.',
                'total_rows' => 0,
                'valid_count' => 0,
                'invalid_count' => 0,
                'warning_count' => 0,
                'details' => [],
                'importable_data' => [],
            ];
        }

        // Header check
        $header = array_shift($rows); // Remove row 1 (headings)

        $totalRows = 0;
        $validCount = 0;
        $invalidCount = 0;
        $warningCount = 0;
        $details = [];
        $importableData = [];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // Excel row number (1-based, header was row 1)
            $name = trim((string) ($row[0] ?? ''));
            $nis = trim((string) ($row[1] ?? ''));
            $nisn = trim((string) ($row[2] ?? ''));
            $materi = trim((string) ($row[3] ?? ''));
            $indikator = trim((string) ($row[4] ?? ''));
            $rawScore = trim((string) ($row[5] ?? ''));

            // Skip entirely empty row
            if ($name === '' && $nis === '' && $nisn === '' && $indikator === '') {
                continue;
            }

            $totalRows++;
            $status = 'VALID';
            $issues = [];
            $solution = null;
            $matchedStudent = null;

            // 1. Validate Student by NISN or NIS or Name
            if ($nisn !== '') {
                $matchedStudent = Student::where('nisn', $nisn)->first();
            }
            if (! $matchedStudent && $nis !== '') {
                $matchedStudent = Student::where('nis', $nis)->first();
            }
            if (! $matchedStudent && $name !== '') {
                $matchedStudent = Student::where('name', 'LIKE', $name)->first();
            }

            if (! $matchedStudent) {
                $status = 'INVALID';
                $issues[] = "Siswa tidak ditemukan di sistem (NISN: '{$nisn}', NIS: '{$nis}', Nama: '{$name}').";
                $solution = 'Pastikan NISN atau NIS siswa sudah terdaftar di master data siswa.';
            } else {
                // Name mismatch check
                if ($name !== '' && strtolower($matchedStudent->name) !== strtolower($name)) {
                    $status = ($status === 'INVALID') ? 'INVALID' : 'WARNING';
                    $issues[] = "NIS/NISN cocok dengan data: '{$matchedStudent->name}', tetapi di file tertulis: '{$name}'.";
                    $solution = "Sistem akan menggunakan data siswa resmi dari database ({$matchedStudent->name}).";
                }
            }

            // 2. Validate Indicator & Materi
            if ($indikator === '') {
                $status = 'INVALID';
                $issues[] = 'Nama indikator / surat tidak boleh kosong.';
                $solution = 'Isi nama indikator/surat pada kolom E.';
            }

            // 3. Validate Score
            $scoreVal = null;
            if ($rawScore !== '') {
                if (! is_numeric($rawScore)) {
                    $status = 'INVALID';
                    $issues[] = "Nilai '{$rawScore}' bukan angka.";
                    $solution = 'Ganti nilai dengan angka antara 1 sampai 100, atau kosongkan.';
                } else {
                    $num = (int) $rawScore;
                    if ($num < 0 || $num > 100) {
                        $status = 'INVALID';
                        $issues[] = "Nilai {$num} di luar rentang valid (1-100).";
                        $solution = 'Ubah nilai agar berada di rentang 1 hingga 100.';
                    } elseif ($num === 0) {
                        // 0 is treated as unrated
                        $scoreVal = null;
                        if ($status !== 'INVALID') {
                            $status = 'WARNING';
                            $issues[] = 'Nilai diisi 0 (dianggap belum dinilai / kosong).';
                        }
                    } else {
                        $scoreVal = $num;
                    }
                }
            }

            // 4. Validate Assessment lock status
            if ($matchedStudent) {
                $assessment = StudentAssessment::where('student_id', $matchedStudent->id)
                    ->where('academic_year_id', $academicYearId)
                    ->where('semester_id', $semesterId)
                    ->first();

                if ($assessment && in_array($assessment->status, ['LOCKED', 'APPROVED'])) {
                    $status = 'INVALID';
                    $issues[] = "Raport siswa sudah berstatus {$assessment->status} dan tidak dapat diedit.";
                    $solution = 'Minta Super Admin untuk melakukan unlock / revisi terlebih dahulu.';
                }
            }

            if ($status === 'INVALID') {
                $invalidCount++;
            } elseif ($status === 'WARNING') {
                $warningCount++;
                $validCount++;
            } else {
                $validCount++;
            }

            $detailItem = [
                'row' => $rowNum,
                'excel_name' => $name,
                'db_name' => $matchedStudent ? $matchedStudent->name : '-',
                'nis' => $nis,
                'nisn' => $nisn,
                'materi' => $materi ?: 'Tahfizh / Tahsin',
                'indikator' => $indikator,
                'raw_score' => $rawScore,
                'parsed_score' => $scoreVal,
                'status' => $status,
                'issues' => implode(' | ', $issues),
                'solution' => $solution,
                'student_id' => $matchedStudent ? $matchedStudent->id : null,
            ];

            $details[] = $detailItem;

            if ($status !== 'INVALID' && $matchedStudent) {
                $importableData[] = $detailItem;
            }
        }

        return [
            'success' => true,
            'total_rows' => $totalRows,
            'valid_count' => $validCount,
            'invalid_count' => $invalidCount,
            'warning_count' => $warningCount,
            'details' => $details,
            'importable_data' => $importableData,
        ];
    }

    public function executeImport(array $importableData, int $academicYearId, int $semesterId, int $teacherUserId): int
    {
        $importedCount = 0;
        $assessmentService = app(AssessmentService::class);

        foreach ($importableData as $data) {
            $student = Student::find($data['student_id']);
            if (! $student) {
                continue;
            }

            $assessment = $assessmentService->getOrCreateAssessment(
                $student,
                $academicYearId,
                $semesterId,
                $teacherUserId
            );

            // Find or create Section
            $sectionName = $data['materi'] ?: 'Tahfizh';
            $section = $assessment->sections()->where('name', $sectionName)->first();
            if (! $section) {
                $section = $assessmentService->addSection($assessment, $sectionName);
            }

            // Find or create Item
            $itemName = $data['indikator'];
            $item = $section->items()->where('name', $itemName)->first();
            if (! $item) {
                $item = $assessmentService->addItem($section, $itemName, $data['parsed_score']);
            } else {
                $assessmentService->updateScore($item, $data['parsed_score']);
            }

            $importedCount++;
        }

        AuditLog::log(
            'IMPORT_EXCEL',
            "Berhasil mengimpor {$importedCount} data nilai dari Excel.",
            null,
            null,
            ['count' => $importedCount]
        );

        return $importedCount;
    }
}
