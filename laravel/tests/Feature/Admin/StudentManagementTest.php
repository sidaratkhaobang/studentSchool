<?php

namespace Tests\Feature\Admin;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->token = $admin->createToken('test')->plainTextToken;
    }

    public function test_admin_can_approve_pending_student(): void
    {
        $student = Student::factory()->create([
            'user_id' => User::factory()->create(['role' => 'student', 'is_active' => false])->id,
            'status' => 'pending',
        ]);

        $response = $this->withToken($this->token)
            ->putJson("/api/admin/students/{$student->id}/status", ['status' => 'approved']);

        $response->assertOk()
            ->assertJsonPath('student.status', 'approved')
            ->assertJsonPath('student.user.is_active', true);
    }

    public function test_admin_can_reject_student(): void
    {
        $student = Student::factory()->create([
            'user_id' => User::factory()->create(['role' => 'student'])->id,
            'status' => 'pending',
        ]);

        $response = $this->withToken($this->token)
            ->putJson("/api/admin/students/{$student->id}/status", ['status' => 'rejected']);

        $response->assertOk()
            ->assertJsonPath('student.status', 'rejected');
    }

    public function test_invalid_student_status_is_rejected(): void
    {
        $student = Student::factory()->create([
            'user_id' => User::factory()->create(['role' => 'student'])->id,
        ]);

        $response = $this->withToken($this->token)
            ->putJson("/api/admin/students/{$student->id}/status", ['status' => 'archived']);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    public function test_admin_can_filter_students_by_status(): void
    {
        Student::factory()->create([
            'user_id' => User::factory()->create(['role' => 'student'])->id,
            'status' => 'pending',
        ]);
        Student::factory()->create([
            'user_id' => User::factory()->create(['role' => 'student'])->id,
            'status' => 'approved',
        ]);

        $response = $this->withToken($this->token)->getJson('/api/admin/students?status=pending');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'pending');
    }

    public function test_admin_can_view_student_detail_with_relations(): void
    {
        $student = Student::factory()->create([
            'user_id' => User::factory()->create(['role' => 'student', 'username' => 'detail01'])->id,
        ]);

        $response = $this->withToken($this->token)->getJson("/api/admin/students/{$student->id}");

        $response->assertOk()
            ->assertJsonPath('student.id', $student->id)
            ->assertJsonPath('student.user.username', 'detail01');
    }
}
