<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeeklyEnrollmentFactory extends Factory
{
    public function definition(): array
    {
        $weekStart = fake()->dateTimeBetween('-4 weeks', '+4 weeks')->format('Y-m-d');

        return [
            'student_id' => Student::factory(),
            'week_start' => $weekStart,
            'week_end'   => date('Y-m-d', strtotime($weekStart . ' +4 days')),
            'status'     => 'draft',
            'note'       => null,
        ];
    }
}
