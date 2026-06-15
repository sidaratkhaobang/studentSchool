<?php

namespace Tests\Feature;

use App\Models\EnrollmentCourse;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Models\WeeklyEnrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_returns_system_statistics_and_weekly_trend(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test')->plainTextToken;
        Student::factory()->create(['user_id' => User::factory()->create(['role' => 'student'])->id, 'status' => 'pending']);
        Student::factory()->create(['user_id' => User::factory()->create(['role' => 'student'])->id, 'status' => 'approved']);
        Teacher::factory(2)->create(['is_active' => true]);
        Subject::factory()->create(['is_active' => true]);

        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $student = Student::factory()->create([
            'user_id' => User::factory()->create(['role' => 'student'])->id,
            'status' => 'approved',
        ]);
        WeeklyEnrollment::factory()->create([
            'student_id' => $student->id,
            'week_start' => $weekStart,
            'week_end' => Carbon::parse($weekStart)->endOfWeek(Carbon::FRIDAY)->toDateString(),
            'status' => 'submitted',
        ]);

        $response = $this->withToken($token)->getJson('/api/admin/dashboard');

        $response->assertOk()
            ->assertJsonPath('stats.students.pending', 1)
            ->assertJsonPath('stats.students.approved', 2)
            ->assertJsonPath('stats.teachers.active', 2)
            ->assertJsonPath('stats.subjects.active', 1)
            ->assertJsonPath('stats.enrollments.this_week', 1)
            ->assertJsonCount(4, 'weekly_trend');
    }

    public function test_student_dashboard_returns_current_week_schedule_and_recent_enrollments(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $student = Student::factory()->create(['user_id' => $user->id, 'status' => 'approved']);
        $token = $user->createToken('test')->plainTextToken;
        $subject = Subject::factory()->create(['is_active' => true]);

        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $enrollment = WeeklyEnrollment::factory()->create([
            'student_id' => $student->id,
            'week_start' => $weekStart,
            'week_end' => Carbon::parse($weekStart)->endOfWeek(Carbon::FRIDAY)->toDateString(),
        ]);
        EnrollmentCourse::create([
            'weekly_enrollment_id' => $enrollment->id,
            'subject_id' => $subject->id,
            'day_of_week' => 'monday',
            'hours' => 2,
        ]);

        $response = $this->withToken($token)->getJson('/api/student/dashboard');

        $response->assertOk()
            ->assertJsonPath('student.id', $student->id)
            ->assertJsonPath('current_enrollment.id', $enrollment->id)
            ->assertJsonPath('schedule.monday.total_hours', 2)
            ->assertJsonPath('total_hours_week', 2)
            ->assertJsonCount(1, 'recent_enrollments');
    }
}
