<?php

namespace Tests\Feature\Admin;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->token = $this->admin->createToken('test')->plainTextToken;
    }

    // TC-ADMIN-T001
    public function test_admin_can_create_teacher(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/admin/teachers', [
            'first_name_th' => 'วิชัย',
            'last_name_th'  => 'ดีมาก',
            'first_name_en' => 'Wichai',
            'last_name_en'  => 'Deemak',
            'email'         => 'wichai@school.ac.th',
            'phone'         => '0891234567',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('teacher.first_name_th', 'วิชัย');

        $this->assertDatabaseHas('teachers', ['email' => 'wichai@school.ac.th']);
    }

    // TC-ADMIN-T002
    public function test_create_teacher_fails_with_duplicate_email(): void
    {
        Teacher::factory()->create(['email' => 'wichai@school.ac.th']);

        $response = $this->withToken($this->token)->postJson('/api/admin/teachers', [
            'first_name_th' => 'อื่น', 'last_name_th' => 'คน',
            'first_name_en' => 'Other', 'last_name_en' => 'Person',
            'email'         => 'wichai@school.ac.th',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    // TC-ADMIN-T003
    public function test_admin_can_get_paginated_teachers(): void
    {
        Teacher::factory(20)->create();

        $response = $this->withToken($this->token)->getJson('/api/admin/teachers?page=1&per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'current_page', 'total', 'per_page'])
            ->assertJsonCount(10, 'data');
    }

    // TC-ADMIN-T004
    public function test_admin_can_update_teacher(): void
    {
        $teacher = Teacher::factory()->create();

        $response = $this->withToken($this->token)->putJson("/api/admin/teachers/{$teacher->id}", [
            'first_name_th' => $teacher->first_name_th,
            'last_name_th'  => $teacher->last_name_th,
            'first_name_en' => $teacher->first_name_en,
            'last_name_en'  => $teacher->last_name_en,
            'email'         => $teacher->email,
            'phone'         => '0999999999',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('teachers', ['id' => $teacher->id, 'phone' => '0999999999']);
    }

    // TC-ADMIN-T005
    public function test_admin_can_delete_teacher_with_no_students(): void
    {
        $teacher = Teacher::factory()->create();

        $response = $this->withToken($this->token)->deleteJson("/api/admin/teachers/{$teacher->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('teachers', ['id' => $teacher->id]);
    }

    // TC-ADMIN-T006
    public function test_admin_cannot_delete_teacher_who_has_students(): void
    {
        $teacher = Teacher::factory()->create();
        $studentUser = User::factory()->create(['role' => 'student']);
        Student::factory()->create(['user_id' => $studentUser->id, 'advisor_teacher_id' => $teacher->id]);

        $response = $this->withToken($this->token)->deleteJson("/api/admin/teachers/{$teacher->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('teachers', ['id' => $teacher->id]);
    }

    // TC-ADMIN-T007
    public function test_student_cannot_access_admin_endpoints(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $token = $student->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/admin/teachers');

        $response->assertStatus(403);
    }
}
