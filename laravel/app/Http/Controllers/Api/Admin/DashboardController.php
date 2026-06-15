<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\WeeklyEnrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $currentWeekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();

        $stats = [
            'students' => [
                'total'    => Student::count(),
                'pending'  => Student::pending()->count(),
                'approved' => Student::approved()->count(),
                'rejected' => Student::where('status', 'rejected')->count(),
            ],
            'teachers' => [
                'total'  => Teacher::count(),
                'active' => Teacher::active()->count(),
            ],
            'subjects' => [
                'total'  => Subject::count(),
                'active' => Subject::active()->count(),
            ],
            'enrollments' => [
                'this_week' => WeeklyEnrollment::whereDate('week_start', $currentWeekStart)->count(),
                'submitted' => WeeklyEnrollment::whereDate('week_start', $currentWeekStart)
                    ->where('status', 'submitted')->count(),
                'approved'  => WeeklyEnrollment::whereDate('week_start', $currentWeekStart)
                    ->where('status', 'approved')->count(),
            ],
        ];

        $weeklyTrend = [];
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek(Carbon::MONDAY)->toDateString();
            $weekEnd   = Carbon::now()->subWeeks($i)->endOfWeek(Carbon::FRIDAY)->toDateString();
            $weeklyTrend[] = [
                'week'  => "สัปดาห์ที่ {$weekStart}",
                'count' => WeeklyEnrollment::whereDate('week_start', $weekStart)->count(),
            ];
        }

        return response()->json([
            'stats'        => $stats,
            'weekly_trend' => $weeklyTrend,
        ]);
    }
}
