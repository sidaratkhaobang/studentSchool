<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\WeeklyEnrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user()->teacher;

        $subjects = $teacher->subjects()
            ->withCount('enrollmentCourses')
            ->orderBy('subject_code')
            ->get();

        $classrooms = $teacher->advisingStudents()
            ->select('grade_level', DB::raw('count(*) as students_count'))
            ->groupBy('grade_level')
            ->orderBy('grade_level')
            ->get();

        $advisingStudents = $teacher->advisingStudents()
            ->with('user')
            ->orderBy('grade_level')
            ->orderBy('first_name_th')
            ->get();

        $pendingEnrollments = WeeklyEnrollment::query()
            ->where('status', 'submitted')
            ->whereHas('student', fn ($query) => $query->where('advisor_teacher_id', $teacher->id))
            ->count();

        return response()->json([
            'teacher' => $teacher,
            'stats' => [
                'subjects' => $subjects->count(),
                'classrooms' => $classrooms->count(),
                'advising_students' => $advisingStudents->count(),
                'pending_enrollments' => $pendingEnrollments,
            ],
            'subjects' => $subjects,
            'classrooms' => $classrooms,
            'advising_students' => $advisingStudents,
        ]);
    }
}
