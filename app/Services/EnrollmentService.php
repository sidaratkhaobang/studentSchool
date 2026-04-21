<?php

namespace App\Services;

use App\Models\EnrollmentCourse;
use App\Models\WeeklyEnrollment;

class EnrollmentService
{
    const MAX_DAILY_HOURS = 6.0;

    public function addCourse(
        WeeklyEnrollment $enrollment,
        int $subjectId,
        string $day,
        float $hours,
        ?string $startTime = null,
        ?string $endTime = null
    ): array {
        $currentHours = $enrollment->getDailyHours($day);

        if ($currentHours + $hours > self::MAX_DAILY_HOURS) {
            $remaining = self::MAX_DAILY_HOURS - $currentHours;
            return [
                'success' => false,
                'message' => "เกินจำนวนชั่วโมงสูงสุดต่อวัน (สูงสุด " . self::MAX_DAILY_HOURS . " ชั่วโมง, เหลือ {$remaining} ชั่วโมง)",
            ];
        }

        $course = EnrollmentCourse::create([
            'weekly_enrollment_id' => $enrollment->id,
            'subject_id'           => $subjectId,
            'day_of_week'          => $day,
            'hours'                => $hours,
            'start_time'           => $startTime,
            'end_time'             => $endTime,
        ]);

        return [
            'success' => true,
            'course'  => $course,
        ];
    }

    public function getDailyHoursSummary(WeeklyEnrollment $enrollment): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $summary = [];

        foreach ($days as $day) {
            $hours = $enrollment->getDailyHours($day);
            $summary[$day] = [
                'hours'     => $hours,
                'remaining' => self::MAX_DAILY_HOURS - $hours,
                'is_full'   => $hours >= self::MAX_DAILY_HOURS,
            ];
        }

        return $summary;
    }
}
