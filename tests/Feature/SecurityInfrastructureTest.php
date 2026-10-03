<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_security_headers_are_present_in_responses(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $this->assertTrue($response->headers->has('Permissions-Policy'));
    }

    public function test_unauthorized_user_cannot_access_super_admin_routes(): void
    {
        $guru = User::where('email', 'guru@alathifah.sch.id')->first();
        $this->actingAs($guru);

        // Guru attempts to access admin teachers management -> MUST be Forbidden (403)
        $response = $this->get(route('admin.teachers.index'));
        $response->assertForbidden();

        // Guru attempts to access admin students management -> MUST be Forbidden (403)
        $responseStudents = $this->get(route('admin.students.index'));
        $responseStudents->assertForbidden();

        // Guru attempts to access audit trail -> MUST be Forbidden (403)
        $responseAudit = $this->get(route('admin.audit.index'));
        $responseAudit->assertForbidden();
    }

    public function test_failed_login_attempt_is_logged_in_audit_logs(): void
    {
        $initialFailedCount = AuditLog::where('action', 'FAILED_LOGIN')->count();

        $response = $this->post('/login', [
            'login' => 'hacker@attacker.com',
            'password' => 'wrongpassword123',
        ]);

        $response->assertSessionHasErrors('login');

        $this->assertEquals(
            $initialFailedCount + 1,
            AuditLog::where('action', 'FAILED_LOGIN')->count()
        );

        $latestLog = AuditLog::where('action', 'FAILED_LOGIN')->latest()->first();
        $this->assertStringContainsString('hacker@attacker.com', $latestLog->description);
        $this->assertNotNull($latestLog->ip_address);
    }

    public function test_blocked_inactive_user_login_is_logged(): void
    {
        $guru = User::where('email', 'guru@alathifah.sch.id')->first();
        $guru->update(['is_active' => false]);

        $response = $this->post('/login', [
            'login' => 'guru@alathifah.sch.id',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();

        $blockedLog = AuditLog::where('action', 'BLOCKED_LOGIN')->latest()->first();
        $this->assertNotNull($blockedLog);
        $this->assertEquals($guru->id, $blockedLog->user_id);
    }
}
