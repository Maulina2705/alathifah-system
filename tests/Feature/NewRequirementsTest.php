<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AssessmentTemplate;
use App\Models\Deadline;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAssessment;
use App\Models\StudentTeacherAssignment;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NewRequirementsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $it;

    protected User $kepsek;

    protected User $wali;

    protected User $guru;

    protected AcademicYear $ay;

    protected Semester $sem;

    protected Student $student1;

    protected Student $student2;

    protected StudentAssessment $assessment1;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['SUPER ADMIN', 'IT', 'KEPALA SEKOLAH', 'WALI KELAS', 'GURU'] as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        $this->ay = AcademicYear::create(['name' => '2025/2026', 'is_active' => true]);
        $this->sem = Semester::create(['academic_year_id' => $this->ay->id, 'name' => 'Genap', 'is_active' => true]);

        $this->admin = User::factory()->create(['name' => 'Admin Test']);
        $this->admin->assignRole('SUPER ADMIN');

        $this->it = User::factory()->create(['name' => 'IT Test']);
        $this->it->assignRole('IT');

        $this->kepsek = User::factory()->create(['name' => 'Kepsek Test']);
        $this->kepsek->assignRole('KEPALA SEKOLAH');
        TeacherProfile::create(['user_id' => $this->kepsek->id, 'level' => 'SD', 'is_verified' => true]);

        $this->wali = User::factory()->create(['name' => 'Walas Test']);
        $this->wali->assignRole('WALI KELAS');
        TeacherProfile::create(['user_id' => $this->wali->id, 'level' => 'SD', 'is_verified' => true]);

        $this->guru = User::factory()->create(['name' => 'Guru Test']);
        $this->guru->assignRole('GURU');
        TeacherProfile::create(['user_id' => $this->guru->id, 'level' => 'SD', 'is_verified' => true]);

        $this->student1 = Student::create([
            'name' => 'ahmad fathan',
            'nis' => '11001',
            'nisn' => '0011223344',
            'level' => 'SD',
            'gender' => 'L',
        ]);

        $this->student2 = Student::create([
            'name' => 'siti fatimah',
            'nis' => '11002',
            'nisn' => '0011223355',
            'level' => 'SD',
            'gender' => 'P',
        ]);

        $template = AssessmentTemplate::create([
            'name' => 'Template SD',
            'level' => 'SD',
            'created_by_user_id' => $this->admin->id,
            'is_active' => true,
        ]);

        $this->assessment1 = StudentAssessment::create([
            'student_id' => $this->student1->id,
            'teacher_user_id' => $this->guru->id,
            'academic_year_id' => $this->ay->id,
            'semester_id' => $this->sem->id,
            'template_id' => $template->id,
            'status' => 'SUBMITTED',
        ]);

        StudentTeacherAssignment::create([
            'student_id' => $this->student1->id,
            'teacher_user_id' => $this->guru->id,
            'homeroom_user_id' => $this->wali->id,
            'academic_year_id' => $this->ay->id,
            'semester_id' => $this->sem->id,
        ]);
    }

    public function test_student_name_is_always_converted_to_uppercase(): void
    {
        $this->assertEquals('AHMAD FATHAN', $this->student1->name);
        $this->assertEquals('SITI FATIMAH', $this->student2->name);
    }

    public function test_it_cannot_access_audit_logs_but_super_admin_can(): void
    {
        $responseIt = $this->actingAs($this->it)->get(route('admin.audit.index'));
        $responseIt->assertForbidden();

        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.audit.index'));
        $responseAdmin->assertOk();
    }

    public function test_it_and_super_admin_can_access_history(): void
    {
        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.history.index'));
        $responseAdmin->assertOk();

        $responseIt = $this->actingAs($this->it)->get(route('admin.history.index'));
        $responseIt->assertOk();

        $responseGuru = $this->actingAs($this->guru)->get(route('admin.history.index'));
        $responseGuru->assertForbidden();
    }

    public function test_kepsek_it_and_admin_can_manage_deadlines(): void
    {
        $deadline = Deadline::create([
            'academic_year_id' => $this->ay->id,
            'semester_id' => $this->sem->id,
            'title' => 'Deadline Test',
            'deadline_at' => now()->addDays(5),
            'level' => 'SD',
            'is_active' => true,
        ]);

        // Kepsek can view and update
        $resKepsek = $this->actingAs($this->kepsek)->get(route('admin.deadlines.index'));
        $resKepsek->assertOk();

        $resUpdate = $this->actingAs($this->kepsek)->put(route('admin.deadlines.update', $deadline->id), [
            'academic_year_id' => $this->ay->id,
            'semester_id' => $this->sem->id,
            'title' => 'Deadline Kepsek Update',
            'deadline_at' => now()->addDays(7)->format('Y-m-d\TH:i'),
            'level' => 'SD',
            'is_active' => 1,
        ]);
        $resUpdate->assertSessionHas('success');

        // IT can view
        $resIt = $this->actingAs($this->it)->get(route('admin.deadlines.index'));
        $resIt->assertOk();

        // Guru cannot access
        $resGuru = $this->actingAs($this->guru)->get(route('admin.deadlines.index'));
        $resGuru->assertForbidden();
    }

    public function test_it_can_update_printing_progress_status(): void
    {
        $res = $this->actingAs($this->it)->post(route('reports.update_status', $this->assessment1->id), [
            'status' => 'MENUNGGU_CETAK',
        ]);
        $res->assertSessionHas('success');
        $this->assertEquals('MENUNGGU_CETAK', $this->assessment1->fresh()->status);
        $this->assertEquals('Menunggu Antrian Cetak', $this->assessment1->fresh()->status_label);

        $this->actingAs($this->it)->post(route('reports.update_status', $this->assessment1->id), [
            'status' => 'PROSES_CETAK',
        ]);
        $this->assertEquals('PROSES_CETAK', $this->assessment1->fresh()->status);
        $this->assertEquals('Proses Cetak', $this->assessment1->fresh()->status_label);

        $this->actingAs($this->it)->post(route('reports.update_status', $this->assessment1->id), [
            'status' => 'SELESAI',
        ]);
        $this->assertEquals('SELESAI', $this->assessment1->fresh()->status);
        $this->assertEquals('Selesai', $this->assessment1->fresh()->status_label);
    }

    public function test_homeroom_teacher_can_download_bulk_pdf_for_own_class(): void
    {
        $response = $this->actingAs($this->wali)->get(route('reports.homeroom_bulk_pdf'));
        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_system_guide_pdf_can_be_downloaded(): void
    {
        $response = $this->actingAs($this->admin)->get(route('docs.download'));
        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_user_can_login_using_username(): void
    {
        $this->admin->update(['username' => 'adminutama']);

        $response = $this->post(route('login'), [
            'login' => 'adminutama',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_user_can_download_official_single_pdf_report(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.pdf', $this->assessment1->id));
        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertTrue(str_contains($response->headers->get('Content-Disposition'), 'attachment'));
    }
}
