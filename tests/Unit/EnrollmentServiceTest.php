<?php

namespace Tests\Unit;

use App\Models\EnrollmentCourse;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Models\WeeklyEnrollment;
use App\Services\EnrollmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private EnrollmentService $service;
    private WeeklyEnrollment $enrollment;
    private Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EnrollmentService();

        $user = User::factory()->create(['role' => 'student']);
        $student = Student::factory()->create(['user_id' => $user->id]);
        $this->enrollment = WeeklyEnrollment::factory()->create([
            'student_id' => $student->id,
            'week_start' => '2026-04-21',
            'week_end'   => '2026-04-25',
        ]);
        $this->subject = Subject::factory()->create(['is_active' => true]);
    }

    public function test_add_course_success(): void
    {
        $result = $this->service->addCourse($this->enrollment, $this->subject->id, 'monday', 2.0);

        $this->assertTrue($result['success']);
        $this->assertInstanceOf(EnrollmentCourse::class, $result['course']);
        $this->assertEquals(2.0, $result['course']->hours);
    }

    public function test_add_course_fails_when_exceeds_daily_limit(): void
    {
        // Add 5 hours first
        $this->service->addCourse($this->enrollment, $this->subject->id, 'monday', 5.0);

        // Try to add 2 more
        $subject2 = Subject::factory()->create(['is_active' => true]);
        $result = $this->service->addCourse($this->enrollment, $subject2->id, 'monday', 2.0);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('เกินจำนวนชั่วโมง', $result['message']);
    }

    public function test_add_course_allows_exactly_6_hours(): void
    {
        $this->service->addCourse($this->enrollment, $this->subject->id, 'monday', 5.0);

        $subject2 = Subject::factory()->create(['is_active' => true]);
        $result = $this->service->addCourse($this->enrollment, $subject2->id, 'monday', 1.0);

        $this->assertTrue($result['success']);
    }

    public function test_get_daily_hours_summary(): void
    {
        $this->service->addCourse($this->enrollment, $this->subject->id, 'monday', 3.0);

        $summary = $this->service->getDailyHoursSummary($this->enrollment);

        $this->assertArrayHasKey('monday', $summary);
        $this->assertEquals(3.0, $summary['monday']['hours']);
        $this->assertEquals(3.0, $summary['monday']['remaining']);
        $this->assertFalse($summary['monday']['is_full']);

        $this->assertEquals(6.0, $summary['tuesday']['remaining']);
    }

    public function test_daily_hours_is_full_when_6_hours_reached(): void
    {
        $this->service->addCourse($this->enrollment, $this->subject->id, 'friday', 6.0);

        $summary = $this->service->getDailyHoursSummary($this->enrollment);

        $this->assertTrue($summary['friday']['is_full']);
        $this->assertEquals(0.0, $summary['friday']['remaining']);
    }
}
