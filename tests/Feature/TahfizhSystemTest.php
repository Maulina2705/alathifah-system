<?php

namespace Tests\Feature;

use App\Models\AssessmentTemplate;
use App\Models\AssessmentTemplateItem;
use App\Models\Student;
use App\Models\StudentAssessment;
use App\Models\StudentAssessmentItem;
use App\Models\User;
use App\Services\AssessmentService;
use App\Services\ReportPdfService;
use App\Services\WorkflowService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TahfizhSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Master Raport Tahfizh');
    }

    public function test_admin_can_login_and_access_dashboard()
    {
        $response = $this->post('/login', [
            'email' => 'admin@alathifah.sch.id',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $dashboardResponse = $this->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Super Admin Tahfizh');
        $dashboardResponse->assertSee('Progress Input Nilai Raport Tahfizh');
    }

    public function test_guru_can_access_my_students_page()
    {
        $guru = User::where('email', 'guru@alathifah.sch.id')->first();
        $this->actingAs($guru);

        $response = $this->get(route('teacher.students.index'));
        $response->assertStatus(200);
        $response->assertSee('MUHAMMAD AZZAM ALGHIFARI');
    }

    public function test_assessment_score_service_stores_score_and_omits_zero()
    {
        $assessmentService = app(AssessmentService::class);
        $item = StudentAssessmentItem::first();

        // Update with valid score
        $assessmentService->updateScore($item, 95);
        $this->assertEquals(95, $item->fresh()->score);
        $this->assertTrue($item->fresh()->hasScore());

        // Update with 0 -> should become null (belum dinilai)
        $assessmentService->updateScore($item, 0);
        $this->assertNull($item->fresh()->score);
        $this->assertFalse($item->fresh()->hasScore());
    }

    public function test_student_customization_does_not_mutate_blueprint_template()
    {
        $template = AssessmentTemplate::first();
        $initialTemplateItemCount = AssessmentTemplateItem::count();

        $assessment = StudentAssessment::first();
        $assessmentService = app(AssessmentService::class);

        // Add custom section and item specifically to student assessment
        $sec = $assessmentService->addSection($assessment, 'Hafalan Tambahan Siswa');
        $item = $assessmentService->addItem($sec, 'Surat Al-Baqarah 1-10', 98);

        // Verify student has new item
        $this->assertEquals('Surat Al-Baqarah 1-10', $item->name);
        $this->assertEquals(98, $item->score);

        // Verify template items count remains unchanged!
        $this->assertEquals($initialTemplateItemCount, AssessmentTemplateItem::count());
    }

    public function test_workflow_draft_to_locked_and_unlock()
    {
        $workflowService = app(WorkflowService::class);
        $guru = User::where('email', 'guru@alathifah.sch.id')->first();
        $wali = User::where('email', 'walikelas@alathifah.sch.id')->first();
        $kepsek = User::where('email', 'kepsek@alathifah.sch.id')->first();
        $admin = User::where('email', 'admin@alathifah.sch.id')->first();

        $assessment = StudentAssessment::first();
        $this->assertEquals('DRAFT', $assessment->status);

        // 1. Submit (Guru)
        $workflowService->submit($assessment, $guru, 'Pengajuan nilai Azzam');
        $this->assertEquals('SUBMITTED', $assessment->fresh()->status);

        // 2. Review (Wali Kelas)
        $workflowService->review($assessment, $wali, 'REVIEWED', 'Disetujui wali kelas');
        $this->assertEquals('REVIEWED', $assessment->fresh()->status);

        // 3. Approve (Kepala Sekolah)
        $workflowService->approve($assessment, $kepsek, 'APPROVED', 'Disetujui kepala sekolah');
        $this->assertEquals('APPROVED', $assessment->fresh()->status);

        // 4. Lock (Admin)
        $workflowService->lock($assessment, $admin);
        $this->assertEquals('LOCKED', $assessment->fresh()->status);
        $this->assertFalse($assessment->fresh()->canEdit());

        // 5. Unlock (Admin)
        $workflowService->unlock($assessment, $admin, 'Revisi nilai tahsin');
        $this->assertEquals('DRAFT', $assessment->fresh()->status);
        $this->assertTrue($assessment->fresh()->canEdit());
    }

    public function test_report_pdf_service_generates_valid_pdf_and_omits_unrated_items()
    {
        $assessment = StudentAssessment::first();
        $pdfService = app(ReportPdfService::class);

        $reportData = $pdfService->getReportData($assessment);
        $this->assertNotEmpty($reportData['sections']);

        // Check each section only includes scored items (> 0)
        foreach ($reportData['sections'] as $section) {
            foreach ($section->items as $item) {
                $this->assertNotNull($item->score);
                $this->assertGreaterThan(0, $item->score);
            }
        }

        // Generate PDF
        $pdf = $pdfService->generateSinglePdf($assessment);
        $output = $pdf->output();

        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF-', $output);
    }

    public function test_teacher_verification_blocks_submit_if_not_verified()
    {
        $guru = User::where('email', 'guru@alathifah.sch.id')->first();
        $profile = $guru->teacherProfile;
        $profile->update(['is_verified' => false]);

        $assessment = StudentAssessment::first();
        $workflowService = app(WorkflowService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Verifikasi Biodata Guru');

        $workflowService->submit($assessment, $guru);
    }

    public function test_login_page_has_eye_toggle_for_password()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('togglePasswordVisibility');
        $response->assertSee('eyeIcon');
        $response->assertSee('eyeSlashIcon');
    }

    public function test_admin_can_download_templates_and_import_student_and_teacher()
    {
        $admin = User::where('email', 'admin@alathifah.sch.id')->first();
        $this->actingAs($admin);

        // 1. Download student template
        $response = $this->get(route('admin.students.download_template'));
        $response->assertStatus(200);
        $this->assertTrue($response->headers->has('content-disposition'));

        // 2. Download teacher template
        $response = $this->get(route('admin.teachers.download_template'));
        $response->assertStatus(200);
        $this->assertTrue($response->headers->has('content-disposition'));

        // 3. Import student using CSV format
        $csvContent = "Nama Lengkap,NIS,NISN,Jenjang,Jenis Kelamin,Tempat Lahir,Tanggal Lahir,Catatan\n";
        $csvContent .= "Siswa Uji Coba,12399,0099887766,SD,L,Pekanbaru,2015-01-01,Anak pintar\n";
        $studentFile = UploadedFile::fake()->createWithContent('students.csv', $csvContent);

        $response = $this->post(route('admin.students.import'), [
            'file' => $studentFile,
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('students', [
            'name' => 'SISWA UJI COBA',
            'nis' => '12399',
            'nisn' => '0099887766',
        ]);

        // 4. Import teacher using CSV format
        $csvContentTeacher = "Nama Lengkap,Email,Password,Role,NIP,NIK,Gelar,Jabatan,Jenjang,No HP\n";
        $csvContentTeacher .= "Ustazah Aisyah,aisyah@alathifah.sch.id,password123,GURU,19900101,147101,S.Pd.I,Guru Tahfizh,SD,081233445566\n";
        $teacherFile = UploadedFile::fake()->createWithContent('teachers.csv', $csvContentTeacher);

        $response = $this->post(route('admin.teachers.import'), [
            'file' => $teacherFile,
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'email' => 'aisyah@alathifah.sch.id',
            'name' => 'Ustazah Aisyah',
        ]);
        $this->assertDatabaseHas('teacher_profiles', [
            'nip' => '19900101',
            'title_degree' => 'S.Pd.I',
        ]);
    }

    public function test_report_preview_html_matches_reference_format()
    {
        $assessment = StudentAssessment::first();
        $pdfService = app(ReportPdfService::class);

        $html = $pdfService->renderHtml($assessment);

        // Header check
        $this->assertStringContainsString('LAPORAN PERKEMBANGAN TAHFIZH AL ATHIFA', $html);
        $this->assertStringContainsString('SD ISLAM RIAU GLOBAL TERPADU', $html);
        // Table check
        $this->assertStringContainsString('KOMPETENSI KETERAMPILAN', $html);
        $this->assertStringContainsString('MATERI', $html);
        $this->assertStringContainsString('NILAI AKHIR', $html);
        $this->assertStringContainsString('Tahsin Tilawah', $html);
        $this->assertStringContainsString('Tahfizh Juz 30', $html);
        // Signatures check
        $this->assertStringContainsString('Wali Kelas', $html);
        $this->assertStringContainsString('Guru Tahfidz', $html);
        $this->assertStringContainsString('Kepala Sekolah', $html);
    }
}
