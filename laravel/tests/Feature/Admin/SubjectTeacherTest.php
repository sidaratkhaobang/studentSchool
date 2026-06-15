<?php

namespace Tests\Feature\Admin;

use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTeacherTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->token = $admin->createToken('test')->plainTextToken;
    }

    public function test_admin_can_assign_teacher_to_subject_as_primary(): void
    {
        $subject = Subject::factory()->create();
        $teacher = Teacher::factory()->create();

        $response = $this->withToken($this->token)->postJson('/api/admin/subject-teachers', [
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'is_primary' => true,
        ]);

        $response->assertCreated()
            ->assertJsonPath('assignment.subject_id', $subject->id)
            ->assertJsonPath('assignment.teacher_id', $teacher->id)
            ->assertJsonPath('assignment.is_primary', true);
    }

    public function test_duplicate_subject_teacher_assignment_is_rejected(): void
    {
        $subject = Subject::factory()->create();
        $teacher = Teacher::factory()->create();
        SubjectTeacher::create(['subject_id' => $subject->id, 'teacher_id' => $teacher->id]);

        $response = $this->withToken($this->token)->postJson('/api/admin/subject-teachers', [
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
        ]);

        $response->assertUnprocessable();
    }

    public function test_setting_second_primary_teacher_replaces_existing_primary(): void
    {
        $subject = Subject::factory()->create();
        $firstTeacher = Teacher::factory()->create();
        $secondTeacher = Teacher::factory()->create();
        $firstAssignment = SubjectTeacher::create([
            'subject_id' => $subject->id,
            'teacher_id' => $firstTeacher->id,
            'is_primary' => true,
        ]);

        $response = $this->withToken($this->token)->postJson('/api/admin/subject-teachers', [
            'subject_id' => $subject->id,
            'teacher_id' => $secondTeacher->id,
            'is_primary' => true,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('subject_teachers', [
            'id' => $firstAssignment->id,
            'is_primary' => false,
        ]);
        $this->assertDatabaseHas('subject_teachers', [
            'subject_id' => $subject->id,
            'teacher_id' => $secondTeacher->id,
            'is_primary' => true,
        ]);
    }

    public function test_admin_can_update_assignment_primary_flag(): void
    {
        $subject = Subject::factory()->create();
        $first = SubjectTeacher::create([
            'subject_id' => $subject->id,
            'teacher_id' => Teacher::factory()->create()->id,
            'is_primary' => true,
        ]);
        $second = SubjectTeacher::create([
            'subject_id' => $subject->id,
            'teacher_id' => Teacher::factory()->create()->id,
            'is_primary' => false,
        ]);

        $response = $this->withToken($this->token)->putJson("/api/admin/subject-teachers/{$second->id}", [
            'subject_id' => $subject->id,
            'teacher_id' => $second->teacher_id,
            'is_primary' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('assignment.is_primary', true);
        $this->assertDatabaseHas('subject_teachers', ['id' => $first->id, 'is_primary' => false]);
    }

    public function test_admin_can_remove_subject_teacher_assignment(): void
    {
        $assignment = SubjectTeacher::create([
            'subject_id' => Subject::factory()->create()->id,
            'teacher_id' => Teacher::factory()->create()->id,
        ]);

        $response = $this->withToken($this->token)
            ->deleteJson("/api/admin/subject-teachers/{$assignment->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('subject_teachers', ['id' => $assignment->id]);
    }
}
