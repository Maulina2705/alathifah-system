<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AssessmentTemplate;
use App\Models\AssessmentTemplateItem;
use App\Models\AssessmentTemplateSection;
use App\Models\Deadline;
use App\Models\ReportSetting;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAssessment;
use App\Models\StudentAssessmentItem;
use App\Models\StudentAssessmentSection;
use App\Models\StudentTeacherAssignment;
use App\Models\TeacherProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles
        $roles = ['SUPER ADMIN', 'GURU', 'WALI KELAS', 'KEPALA SEKOLAH', 'IT'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@alathifah.sch.id'],
            [
                'name' => 'Super Admin Tahfizh',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $admin->update(['username' => 'admin']);
        $admin->syncRoles(['SUPER ADMIN']);

        $itStaff = User::firstOrCreate(
            ['email' => 'it@alathifah.sch.id'],
            [
                'name' => 'Staff IT & Percetakan',
                'username' => 'it',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $itStaff->update(['username' => 'it']);
        $itStaff->syncRoles(['IT']);

        $guru = User::firstOrCreate(
            ['email' => 'guru@alathifah.sch.id'],
            [
                'name' => 'M Rizki Fauzan',
                'username' => 'guru',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $guru->update(['username' => 'guru']);
        $guru->syncRoles(['GURU']);
        TeacherProfile::updateOrCreate(
            ['user_id' => $guru->id],
            [
                'nip' => '199208152019031008',
                'nik' => '1471011508920001',
                'title_degree' => 'S.Pd.I',
                'position' => 'Guru Tahfizh',
                'phone' => '081234567890',
                'level' => 'SD',
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        $wali = User::firstOrCreate(
            ['email' => 'walikelas@alathifah.sch.id'],
            [
                'name' => 'Rima Rahmawati',
                'username' => 'walikelas',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $wali->update(['username' => 'walikelas']);
        $wali->syncRoles(['WALI KELAS']);
        TeacherProfile::updateOrCreate(
            ['user_id' => $wali->id],
            [
                'nip' => '199003122018042001',
                'nik' => '1471011203900002',
                'title_degree' => 'S.Pd., Gr.',
                'position' => 'Wali Kelas',
                'phone' => '081234567891',
                'level' => 'SD',
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        $kepsek = User::firstOrCreate(
            ['email' => 'kepsek@alathifah.sch.id'],
            [
                'name' => 'Era Mutia',
                'username' => 'kepsek',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $kepsek->update(['username' => 'kepsek']);
        $kepsek->syncRoles(['KEPALA SEKOLAH']);
        TeacherProfile::updateOrCreate(
            ['user_id' => $kepsek->id],
            [
                'nip' => '198506242010012015',
                'nik' => '1471012406850003',
                'title_degree' => 'S.Pd, Gr.',
                'position' => 'Kepala Sekolah SD',
                'phone' => '081234567892',
                'level' => 'SD',
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        $kepsekSmp = User::firstOrCreate(
            ['email' => 'kepsek.smp@alathifah.sch.id'],
            [
                'name' => 'Drs. H. M. Syukri',
                'username' => 'kepsek.smp',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $kepsekSmp->update(['username' => 'kepsek.smp']);
        $kepsekSmp->syncRoles(['KEPALA SEKOLAH']);
        TeacherProfile::updateOrCreate(
            ['user_id' => $kepsekSmp->id],
            [
                'nip' => '197904152005011004',
                'nik' => '1471011504790005',
                'title_degree' => 'M.Pd.',
                'position' => 'Kepala Sekolah SMP',
                'phone' => '081234567895',
                'level' => 'SMP',
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        // 3. Academic Years & Semesters
        $ay = AcademicYear::firstOrCreate(
            ['name' => '2025/2026'],
            ['is_active' => true]
        );

        $semGanjil = Semester::firstOrCreate(
            ['academic_year_id' => $ay->id, 'name' => 'Ganjil'],
            ['is_active' => false]
        );

        $semGenap = Semester::firstOrCreate(
            ['academic_year_id' => $ay->id, 'name' => 'Genap'],
            ['is_active' => true]
        );

        // Academic Year 2026/2027
        $ay2 = AcademicYear::firstOrCreate(
            ['name' => '2026/2027'],
            ['is_active' => false]
        );
        Semester::firstOrCreate(['academic_year_id' => $ay2->id, 'name' => 'Ganjil'], ['is_active' => false]);
        Semester::firstOrCreate(['academic_year_id' => $ay2->id, 'name' => 'Genap'], ['is_active' => false]);

        // 4. Report Settings for SD & SMP
        ReportSetting::firstOrCreate(
            ['level' => 'SD'],
            [
                'school_name' => 'SD ISLAM RIAU GLOBAL TERPADU',
                'header_title' => 'LAPORAN PERKEMBANGAN TAHFIZH AL ATHIFA',
                'school_address' => 'Jl. HR. Soebrantas No. 100, Panam, Pekanbaru',
                'city' => 'Pekanbaru',
                'principal_name' => 'Era Mutia, S.Pd, Gr.',
                'principal_nip' => '198506242010012015',
                'homeroom_label' => 'Wali Kelas',
                'teacher_label' => 'Guru Tahfidz',
                'principal_label' => 'Kepala Sekolah',
                'default_report_date' => Carbon::create(2026, 6, 20),
                'paper_size' => 'A4',
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_left' => 15,
                'margin_right' => 15,
                'font_family' => 'sans-serif',
            ]
        );

        ReportSetting::firstOrCreate(
            ['level' => 'SMP'],
            [
                'school_name' => 'SMP ISLAM RIAU GLOBAL TERPADU',
                'header_title' => 'LAPORAN PERKEMBANGAN TAHFIZH AL ATHIFA',
                'school_address' => 'Jl. HR. Soebrantas No. 100, Panam, Pekanbaru',
                'city' => 'Pekanbaru',
                'principal_name' => 'Drs. H. M. Syukri, M.Pd.',
                'principal_nip' => '197904152005011004',
                'homeroom_label' => 'Wali Kelas',
                'teacher_label' => 'Guru Tahfidz',
                'principal_label' => 'Kepala Sekolah',
                'default_report_date' => Carbon::create(2026, 6, 20),
                'paper_size' => 'A4',
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_left' => 15,
                'margin_right' => 15,
                'font_family' => 'sans-serif',
            ]
        );

        // 5. Deadlines
        Deadline::firstOrCreate(
            ['academic_year_id' => $ay->id, 'semester_id' => $semGenap->id, 'level' => 'SEMUA'],
            [
                'title' => 'Batas Akhir Penilaian Raport Tahfizh Semester Genap 2025/2026',
                'deadline_at' => Carbon::create(2026, 12, 20, 23, 59, 0),
                'is_active' => true,
            ]
        );

        // 6. Assessment Template
        $template = AssessmentTemplate::firstOrCreate(
            ['name' => 'Tahfizh Al Athifa - Standar SD'],
            [
                'level' => 'SD',
                'description' => 'Template standar kurikulum Tahsin Tilawah dan Tahfizh Juz 30',
                'created_by_user_id' => $admin->id,
                'is_active' => true,
            ]
        );

        $tahsinSec = AssessmentTemplateSection::firstOrCreate(
            ['assessment_template_id' => $template->id, 'name' => 'Tahsin Tilawah'],
            ['order' => 1]
        );

        $tahsinItems = [
            'Mengenal Huruf Hijaiyah',
            'Mengenal Tanda Baca (Harokat)',
            'Mengenal Tempat Keluar Huruf',
            'Mengenal Bacaan Mad',
            'Mengenal Bacaan Mim Sukun',
            'Mengenal Bacaan Nun Sukun/Tanwin',
            'Mengenal Bacaan Ghunnah',
            'Mengenal Bacaan Qolqolah',
        ];
        foreach ($tahsinItems as $idx => $tItem) {
            AssessmentTemplateItem::firstOrCreate(
                ['assessment_template_section_id' => $tahsinSec->id, 'name' => $tItem],
                ['order' => $idx + 1]
            );
        }

        $juz30Sec = AssessmentTemplateSection::firstOrCreate(
            ['assessment_template_id' => $template->id, 'name' => 'Tahfizh Juz 30'],
            ['order' => 2]
        );

        $juz30Items = [
            'An-Naba', 'An-Naazi\'at', 'Abasa', 'At-Takwiir', 'Al-Infithaar',
            'Al-Muthaffifiin', 'Al-Insyiqaaq', 'Al-Buruuj', 'Al-Balad', 'Asy-Syams',
            'Al-Lail', 'Adh-Dhuhaa', 'Asy-Syarh', 'At-Tiin', 'Al-\'Alaq',
            'Al-Qadr', 'Al-Bayyinah', 'Az-Zalzalah', 'Al-\'Aadiyaat', 'Al-Qaari\'ah',
            'At-Takaatsur', 'Al-\'Ashr', 'Al-Humazah', 'Al-Fiil', 'Quraisy',
            'Al-Maa\'uun', 'Al-Kautsar', 'Al-Kaafiruun', 'An-Nashr', 'Al-Lahab',
            'Al-Ikhlas', 'Al-Falaq', 'An-Naas',
        ];
        foreach ($juz30Items as $idx => $jItem) {
            AssessmentTemplateItem::firstOrCreate(
                ['assessment_template_section_id' => $juz30Sec->id, 'name' => $jItem],
                ['order' => $idx + 1]
            );
        }

        // 7. Students
        $s1 = Student::firstOrCreate(
            ['nisn' => '0147147650'],
            [
                'name' => 'MUHAMMAD AZZAM ALGHIFARI',
                'nis' => '10471',
                'level' => 'SD',
                'gender' => 'L',
                'birth_place' => 'Pekanbaru',
                'birth_date' => Carbon::create(2014, 5, 12),
                'is_active' => true,
            ]
        );

        $s2 = Student::firstOrCreate(
            ['nisn' => '0147147651'],
            [
                'name' => 'FATIMAH AZ-ZAHRA',
                'nis' => '10472',
                'level' => 'SD',
                'gender' => 'P',
                'birth_place' => 'Pekanbaru',
                'birth_date' => Carbon::create(2014, 8, 20),
                'is_active' => true,
            ]
        );

        $s3 = Student::firstOrCreate(
            ['nisn' => '0147147652'],
            [
                'name' => 'AISYAH HUMAIRA',
                'nis' => '10473',
                'level' => 'SD',
                'gender' => 'P',
                'birth_place' => 'Pekanbaru',
                'birth_date' => Carbon::create(2014, 11, 2),
                'is_active' => true,
            ]
        );

        $s4 = Student::firstOrCreate(
            ['nisn' => '0147147653'],
            [
                'name' => 'AHMAD ZAIDAN',
                'nis' => '20101',
                'level' => 'SMP',
                'gender' => 'L',
                'birth_place' => 'Pekanbaru',
                'birth_date' => Carbon::create(2012, 3, 14),
                'is_active' => true,
            ]
        );

        // 8. Assignments
        foreach ([$s1, $s2, $s3] as $student) {
            StudentTeacherAssignment::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $ay->id,
                    'semester_id' => $semGenap->id,
                ],
                [
                    'teacher_user_id' => $guru->id,
                    'homeroom_user_id' => $wali->id,
                ]
            );
        }

        // 9. Student Assessment for Muhammad Azzam Alghifari
        $azzamAssessment = StudentAssessment::firstOrCreate(
            [
                'student_id' => $s1->id,
                'academic_year_id' => $ay->id,
                'semester_id' => $semGenap->id,
            ],
            [
                'teacher_user_id' => $guru->id,
                'template_id' => $template->id,
                'status' => 'DRAFT',
            ]
        );

        // Create customized sections and items for Azzam
        $sec1 = StudentAssessmentSection::firstOrCreate(
            ['student_assessment_id' => $azzamAssessment->id, 'name' => 'Tahsin Tilawah'],
            ['order' => 1]
        );

        $azzamTahsinScores = [
            'Mengenal Huruf Hijaiyah' => 95,
            'Mengenal Tanda Baca (Harokat)' => 95,
            'Mengenal Tempat Keluar Huruf' => 94,
            'Mengenal Bacaan Mad' => 95,
            'Mengenal Bacaan Mim Sukun' => 95,
            'Mengenal Bacaan Nun Sukun/Tanwin' => 95,
            'Mengenal Bacaan Ghunnah' => 94,
            'Mengenal Bacaan Qolqolah' => 95,
        ];
        $i = 1;
        foreach ($azzamTahsinScores as $itemName => $sc) {
            StudentAssessmentItem::updateOrCreate(
                ['student_assessment_section_id' => $sec1->id, 'name' => $itemName],
                ['order' => $i++, 'score' => $sc]
            );
        }

        $sec2 = StudentAssessmentSection::firstOrCreate(
            ['student_assessment_id' => $azzamAssessment->id, 'name' => 'Tahfizh Juz 30'],
            ['order' => 2]
        );

        $azzamJuz30Scores = [
            'An-Naba' => 90,
            'An-Naazi\'at' => 91,
            'Abasa' => 90,
            'At-Takwiir' => 89,
            'Al-Infithaar' => 92,
            'Al-Muthaffifiin' => 90,
            'Al-Insyiqaaq' => 90,
            'Al-Buruuj' => 90,
            'Al-Balad' => 90,
            'Asy-Syams' => 91,
            'Al-Lail' => 95,
            'Adh-Dhuhaa' => 95,
            'Asy-Syarh' => null, // Belum diisi
            'At-Tiin' => null,    // Belum diisi
            'Al-\'Alaq' => null,  // Belum diisi
        ];
        $j = 1;
        foreach ($azzamJuz30Scores as $itemName => $sc) {
            StudentAssessmentItem::updateOrCreate(
                ['student_assessment_section_id' => $sec2->id, 'name' => $itemName],
                ['order' => $j++, 'score' => $sc]
            );
        }

        // Fatimah assessment (ready for demo)
        $fatimahAssessment = StudentAssessment::firstOrCreate(
            [
                'student_id' => $s2->id,
                'academic_year_id' => $ay->id,
                'semester_id' => $semGenap->id,
            ],
            [
                'teacher_user_id' => $guru->id,
                'template_id' => $template->id,
                'status' => 'DRAFT',
            ]
        );
        $fSec1 = StudentAssessmentSection::firstOrCreate(
            ['student_assessment_id' => $fatimahAssessment->id, 'name' => 'Tahsin Tilawah'],
            ['order' => 1]
        );
        StudentAssessmentItem::firstOrCreate(
            ['student_assessment_section_id' => $fSec1->id, 'name' => 'Mengenal Huruf Hijaiyah'],
            ['order' => 1, 'score' => 96]
        );
        StudentAssessmentItem::firstOrCreate(
            ['student_assessment_section_id' => $fSec1->id, 'name' => 'Mengenal Tanda Baca (Harokat)'],
            ['order' => 2, 'score' => 94]
        );
        $fSec2 = StudentAssessmentSection::firstOrCreate(
            ['student_assessment_id' => $fatimahAssessment->id, 'name' => 'Tahfizh Juz 29'],
            ['order' => 2]
        );
        StudentAssessmentItem::firstOrCreate(
            ['student_assessment_section_id' => $fSec2->id, 'name' => 'Al-Mulk'],
            ['order' => 1, 'score' => 92]
        );
        StudentAssessmentItem::firstOrCreate(
            ['student_assessment_section_id' => $fSec2->id, 'name' => 'Al-Qalam'],
            ['order' => 2, 'score' => 93]
        );
    }
}
