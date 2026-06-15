<?php

namespace Tests\Feature\Admin;

use App\Models\EnrollmentCourse;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Models\WeeklyEnrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->token = $admin->createToken('test')->plainTextToken;
    }

    public function test_admin_can_create_subject(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/admin/subjects', [
            'subject_code' => 'MATH101',
            'name_th' => 'คณิตศาสตร์พื้นฐาน',
            'name_en' => 'Basic Mathematics',
            'description' => 'พื้นฐานคณิตศาสตร์',
            'credit_hours' => 3,
            'hours_per_session' => 2,
            'is_active' => true,
        ]);

        $response->assertCreated()
            ->assertJsonPath('subject.subject_code', 'MATH101');

        $this->assertDatabaseHas('subjects', [
            'subject_code' => 'MATH101',
            'is_active' => true,
        ]);
    }

    public function test_create_subject_fails_with_duplicate_code(): void
    {
        Subject::factory()->create(['subject_code' => 'SCI101']);

        $response = $this->withToken($this->token)->postJson('/api/admin/subjects', [
            'subject_code' => 'SCI101',
            'name_th' => 'วิทยาศาสตร์',
            'name_en' => 'Science',
            'credit_hours' => 3,
            'hours_per_session' => 2,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['subject_code']);
    }

    public function test_admin_can_search_and_filter_subjects(): void
    {
        Subject::factory()->create(['subject_code' => 'MATH101', 'name_th' => 'คณิตศาสตร์', 'is_active' => true]);
        Subject::factory()->create(['subject_code' => 'ART101', 'name_th' => 'ศิลปะ', 'is_active' => false]);

        $response = $this->withToken($this->token)
            ->getJson('/api/admin/subjects?search=MATH&is_active=1&per_page=10');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.subject_code', 'MATH101');
    }

    public function test_admin_can_update_subject_and_deactivate_it(): void
    {
        $subject = Subject::factory()->create(['subject_code' => 'ENG101', 'is_active' => true]);

        $response = $this->withToken($this->token)->putJson("/api/admin/subjects/{$subject->id}", [
            'subject_code' => 'ENG101',
            'name_th' => 'ภาษาอังกฤษ',
            'name_en' => 'English',
            'description' => 'updated',
            'credit_hours' => 2,
            'hours_per_session' => 1,
            'is_active' => false,
        ]);

        $response->assertOk()
            ->assertJsonPath('subject.is_active', false);

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'description' => 'updated',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_subject_without_enrollments(): void
    {
        $subject = Subject::factory()->create();

        $response = $this->withToken($this->token)->deleteJson("/api/admin/subjects/{$subject->id}");

        $response->assertOk();
        $this->assertSoftDeleted('subjects', ['id' => $subject->id]);
    }

    public function test_admin_cannot_delete_subject_with_enrollments(): void
    {
        $subject = Subject::factory()->create();
        $student = Student::factory()->create([
            'user_id' => User::factory()->create(['role' => 'student'])->id,
            'status' => 'approved',
        ]);
        $enrollment = WeeklyEnrollment::factory()->create(['student_id' => $student->id]);
        EnrollmentCourse::create([
            'weekly_enrollment_id' => $enrollment->id,
            'subject_id' => $subject->id,
            'day_of_week' => 'monday',
            'hours' => 1,
        ]);

        $response = $this->withToken($this->token)->deleteJson("/api/admin/subjects/{$subject->id}");

        $response->assertUnprocessable();
        $this->assertDatabaseHas('subjects', ['id' => $subject->id, 'deleted_at' => null]);
    }
}
