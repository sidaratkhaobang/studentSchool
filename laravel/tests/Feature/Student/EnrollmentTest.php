<?php

namespace Tests\Feature\Student;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Models\WeeklyEnrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Student $student;
    private string $token;
    private Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $this->student = Student::factory()->create([
            'user_id' => $this->user->id,
            'status'  => 'approved',
        ]);
        $this->token   = $this->user->createToken('test')->plainTextToken;
        $this->subject = Subject::factory()->create(['is_active' => true]);
    }

    // TC-STUDENT-E001
    public function test_approved_student_can_create_weekly_schedule(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/student/enrollments', [
            'week_start' => '2026-04-21',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('enrollment.status', 'draft');

        $this->assertDatabaseHas('weekly_enrollments', [
            'student_id' => $this->student->id,
            'status'     => 'draft',
        ]);
    }

    // TC-STUDENT-E002
    public function test_duplicate_weekly_schedule_rejected(): void
    {
        WeeklyEnrollment::factory()->create([
            'student_id' => $this->student->id,
            'week_start' => '2026-04-21',
            'week_end'   => '2026-04-25',
        ]);

        $response = $this->withToken($this->token)->postJson('/api/student/enrollments', [
            'week_start' => '2026-04-21',
        ]);

        $response->assertStatus(422);
    }

    // TC-STUDENT-E003
    public function test_student_can_add_course_to_schedule(): void
    {
        $enrollment = WeeklyEnrollment::factory()->create([
            'student_id' => $this->student->id,
            'week_start' => '2026-04-21',
            'week_end'   => '2026-04-25',
            'status'     => 'draft',
        ]);

        $response = $this->withToken($this->token)->postJson("/api/student/enrollments/{$enrollment->id}/courses", [
            'subject_id'  => $this->subject->id,
            'day_of_week' => 'monday',
            'hours'       => 2,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('enrollment_courses', [
            'weekly_enrollment_id' => $enrollment->id,
            'subject_id'           => $this->subject->id,
            'day_of_week'          => 'monday',
            'hours'                => 2,
        ]);
    }

    // TC-STUDENT-E004
    public function test_adding_course_that_exceeds_daily_6_hours_is_rejected(): void
    {
        $enrollment = WeeklyEnrollment::factory()->create([
            'student_id' => $this->student->id,
            'week_start' => '2026-04-21',
            'week_end'   => '2026-04-25',
            'status'     => 'draft',
        ]);

        // Add 5 hours first
        $this->withToken($this->token)->postJson("/api/student/enrollments/{$enrollment->id}/courses", [
            'subject_id' => $this->subject->id, 'day_of_week' => 'monday', 'hours' => 5,
        ]);

        // Try to add 2 more (total 7 > 6)
        $subject2 = Subject::factory()->create(['is_active' => true]);
        $response = $this->withToken($this->token)->postJson("/api/student/enrollments/{$enrollment->id}/courses", [
            'subject_id' => $subject2->id, 'day_of_week' => 'monday', 'hours' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'เกินจำนวนชั่วโมง'));
    }

    // TC-STUDENT-E005
    public function test_adding_course_to_reach_exactly_6_hours_is_allowed(): void
    {
        $enrollment = WeeklyEnrollment::factory()->create([
            'student_id' => $this->student->id, 'week_start' => '2026-04-21',
            'week_end' => '2026-04-25', 'status' => 'draft',
        ]);

        $this->withToken($this->token)->postJson("/api/student/enrollments/{$enrollment->id}/courses", [
            'subject_id' => $this->subject->id, 'day_of_week' => 'monday', 'hours' => 5,
        ]);

        $subject2 = Subject::factory()->create(['is_active' => true]);
        $response = $this->withToken($this->token)->postJson("/api/student/enrollments/{$enrollment->id}/courses", [
            'subject_id' => $subject2->id, 'day_of_week' => 'monday', 'hours' => 1,
        ]);

        $response->assertStatus(201);
    }

    // TC-STUDENT-E006
    public function test_cannot_add_inactive_subject(): void
    {
        $inactiveSubject = Subject::factory()->create(['is_active' => false]);
        $enrollment = WeeklyEnrollment::factory()->create([
            'student_id' => $this->student->id, 'week_start' => '2026-04-21',
            'week_end' => '2026-04-25', 'status' => 'draft',
        ]);

        $response = $this->withToken($this->token)->postJson("/api/student/enrollments/{$enrollment->id}/courses", [
            'subject_id' => $inactiveSubject->id, 'day_of_week' => 'monday', 'hours' => 1,
        ]);

        $response->assertStatus(422);
    }

    // TC-STUDENT-E008
    public function test_cannot_modify_submitted_schedule(): void
    {
        $enrollment = WeeklyEnrollment::factory()->create([
            'student_id' => $this->student->id, 'week_start' => '2026-04-21',
            'week_end' => '2026-04-25', 'status' => 'submitted',
        ]);

        $response = $this->withToken($this->token)->postJson("/api/student/enrollments/{$enrollment->id}/courses", [
            'subject_id' => $this->subject->id, 'day_of_week' => 'monday', 'hours' => 1,
        ]);

        $response->assertStatus(422);
    }

    // TC-STUDENT-E009
    public function test_student_can_submit_schedule(): void
    {
        $enrollment = WeeklyEnrollment::factory()->create([
            'student_id' => $this->student->id, 'week_start' => '2026-04-21',
            'week_end' => '2026-04-25', 'status' => 'draft',
        ]);

        $this->withToken($this->token)->postJson("/api/student/enrollments/{$enrollment->id}/courses", [
            'subject_id' => $this->subject->id, 'day_of_week' => 'monday', 'hours' => 2,
        ]);

        $response = $this->withToken($this->token)->putJson("/api/student/enrollments/{$enrollment->id}/submit");

        $response->assertStatus(200)->assertJsonPath('enrollment.status', 'submitted');
    }

    // TC-STUDENT-E010
    public function test_cannot_submit_empty_schedule(): void
    {
        $enrollment = WeeklyEnrollment::factory()->create([
            'student_id' => $this->student->id, 'week_start' => '2026-04-21',
            'week_end' => '2026-04-25', 'status' => 'draft',
        ]);

        $response = $this->withToken($this->token)->putJson("/api/student/enrollments/{$enrollment->id}/submit");

        $response->assertStatus(422);
    }

    // TC-STUDENT-E011
    public function test_pending_student_cannot_enroll(): void
    {
        $pendingUser = User::factory()->create(['role' => 'student']);
        Student::factory()->create(['user_id' => $pendingUser->id, 'status' => 'pending']);
        $token = $pendingUser->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/student/enrollments', [
            'week_start' => '2026-04-21',
        ]);

        $response->assertStatus(403);
    }

    // TC-SEC-004
    public function test_student_cannot_access_other_student_enrollment(): void
    {
        $otherUser = User::factory()->create(['role' => 'student']);
        $otherStudent = Student::factory()->create(['user_id' => $otherUser->id]);
        $otherEnrollment = WeeklyEnrollment::factory()->create([
            'student_id' => $otherStudent->id, 'week_start' => '2026-04-14',
            'week_end' => '2026-04-18',
        ]);

        $response = $this->withToken($this->token)->getJson("/api/student/enrollments/{$otherEnrollment->id}");

        $response->assertStatus(403);
    }
}
