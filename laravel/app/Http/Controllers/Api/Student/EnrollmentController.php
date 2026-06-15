<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\EnrollmentRequest;
use App\Models\Subject;
use App\Models\WeeklyEnrollment;
use App\Services\EnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EnrollmentController extends Controller
{
    public function __construct(private readonly EnrollmentService $enrollmentService) {}

    public function index(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        $enrollments = WeeklyEnrollment::where('student_id', $student->id)
            ->withCount('courses')
            ->orderBy('week_start', 'desc')
            ->paginate($request->integer('per_page', 10));

        return response()->json($enrollments);
    }

    public function store(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (! $student->isApproved()) {
            return response()->json(['message' => 'บัญชีของคุณยังไม่ได้รับการอนุมัติ'], 403);
        }

        $request->validate(['week_start' => 'required|date|date_format:Y-m-d']);

        $requestedDate = Carbon::parse($request->week_start)->toDateString();
        $weekStart = Carbon::parse($requestedDate)->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekEnd   = Carbon::parse($weekStart)->endOfWeek(Carbon::FRIDAY)->toDateString();

        $exists = WeeklyEnrollment::where('student_id', $student->id)
            ->where(function ($query) use ($requestedDate, $weekStart) {
                $query->whereDate('week_start', $weekStart)
                    ->orWhere(function ($query) use ($requestedDate) {
                        $query->whereDate('week_start', '<=', $requestedDate)
                            ->whereDate('week_end', '>=', $requestedDate);
                    });
            })
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'มีตารางเรียนของสัปดาห์นี้อยู่แล้ว'], 422);
        }

        $enrollment = WeeklyEnrollment::create([
            'student_id' => $student->id,
            'week_start' => $weekStart,
            'week_end'   => $weekEnd,
            'status'     => 'draft',
        ]);

        return response()->json([
            'message'    => 'สร้างตารางเรียนสำเร็จ',
            'enrollment' => $enrollment,
        ], 201);
    }

    public function show(Request $request, WeeklyEnrollment $enrollment): JsonResponse
    {
        $this->authorizeEnrollment($request, $enrollment);

        $enrollment->load(['courses.subject.teachers']);
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $schedule = [];

        foreach ($days as $day) {
            $courses = $enrollment->courses->where('day_of_week', $day)->values();
            $schedule[$day] = [
                'courses'     => $courses,
                'total_hours' => $courses->sum('hours'),
            ];
        }

        return response()->json([
            'enrollment' => $enrollment,
            'schedule'   => $schedule,
            'total_hours' => $enrollment->getTotalHours(),
        ]);
    }

    public function addCourse(EnrollmentRequest $request, WeeklyEnrollment $enrollment): JsonResponse
    {
        $this->authorizeEnrollment($request, $enrollment);

        if (! $enrollment->canModify()) {
            return response()->json(['message' => 'ไม่สามารถแก้ไขตารางที่ส่งแล้วได้'], 422);
        }

        $subject = Subject::findOrFail($request->subject_id);

        if (! $subject->is_active) {
            return response()->json(['message' => 'รายวิชานี้ไม่เปิดให้ลงทะเบียน'], 422);
        }

        $result = $this->enrollmentService->addCourse(
            $enrollment,
            $request->subject_id,
            $request->day_of_week,
            $request->hours,
            $request->start_time,
            $request->end_time
        );

        if (! $result['success']) {
            return response()->json(['message' => $result['message']], 422);
        }

        return response()->json([
            'message' => 'เพิ่มรายวิชาสำเร็จ',
            'course'  => $result['course']->load('subject'),
        ], 201);
    }

    public function removeCourse(Request $request, WeeklyEnrollment $enrollment, int $courseId): JsonResponse
    {
        $this->authorizeEnrollment($request, $enrollment);

        if (! $enrollment->canModify()) {
            return response()->json(['message' => 'ไม่สามารถแก้ไขตารางที่ส่งแล้วได้'], 422);
        }

        $course = $enrollment->courses()->findOrFail($courseId);
        $course->delete();

        return response()->json(['message' => 'ลบรายวิชาออกจากตารางสำเร็จ']);
    }

    public function submit(Request $request, WeeklyEnrollment $enrollment): JsonResponse
    {
        $this->authorizeEnrollment($request, $enrollment);

        if (! $enrollment->isDraft()) {
            return response()->json(['message' => 'ตารางนี้ส่งไปแล้ว'], 422);
        }

        if ($enrollment->courses()->count() === 0) {
            return response()->json(['message' => 'ไม่สามารถส่งตารางว่างได้ กรุณาเพิ่มรายวิชาก่อน'], 422);
        }

        $enrollment->update(['status' => 'submitted']);

        return response()->json([
            'message'    => 'ส่งตารางเรียนสำเร็จ',
            'enrollment' => $enrollment->fresh(),
        ]);
    }

    public function subjects(Request $request): JsonResponse
    {
        $subjects = Subject::active()
            ->with(['teachers' => fn ($q) => $q->where('is_primary', true)])
            ->orderBy('subject_code')
            ->get();

        return response()->json(['subjects' => $subjects]);
    }

    private function authorizeEnrollment(Request $request, WeeklyEnrollment $enrollment): void
    {
        $student = $request->user()->student;

        if ($enrollment->student_id !== $student->id) {
            abort(403, 'ไม่มีสิทธิ์เข้าถึงข้อมูลนี้');
        }
    }
}
