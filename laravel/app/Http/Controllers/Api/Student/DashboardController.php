<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\WeeklyEnrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekEnd   = Carbon::now()->endOfWeek(Carbon::FRIDAY)->toDateString();

        $enrollment = WeeklyEnrollment::where('student_id', $student->id)
            ->where('week_start', $weekStart)
            ->with(['courses.subject.teachers'])
            ->first();

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $schedule = [];

        foreach ($days as $day) {
            $courses = $enrollment
                ? $enrollment->courses->where('day_of_week', $day)->values()
                : collect();

            $schedule[$day] = [
                'courses'     => $courses,
                'total_hours' => $courses->sum('hours'),
            ];
        }

        $recentEnrollments = WeeklyEnrollment::where('student_id', $student->id)
            ->orderBy('week_start', 'desc')
            ->limit(5)
            ->withCount('courses')
            ->get();

        return response()->json([
            'student'            => $student->load('advisor'),
            'week_start'         => $weekStart,
            'week_end'           => $weekEnd,
            'current_enrollment' => $enrollment,
            'schedule'           => $schedule,
            'total_hours_week'   => $enrollment ? $enrollment->getTotalHours() : 0,
            'recent_enrollments' => $recentEnrollments,
        ]);
    }
}
