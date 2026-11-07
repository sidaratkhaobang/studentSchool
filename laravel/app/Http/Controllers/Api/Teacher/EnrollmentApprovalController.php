<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\WeeklyEnrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentApprovalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user()->teacher;

        $query = WeeklyEnrollment::query()
            ->with(['student.user', 'student.advisor', 'courses.subject'])
            ->whereHas('student', fn ($q) => $q->where('advisor_teacher_id', $teacher->id));

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['submitted', 'approved', 'rejected']);
        }

        return response()->json(
            $query->orderBy('week_start', 'desc')
                ->paginate($request->integer('per_page', 15))
        );
    }

    public function show(Request $request, WeeklyEnrollment $enrollment): JsonResponse
    {
        $this->authorizeAdvisor($request, $enrollment);

        $enrollment->load(['student.user', 'student.advisor', 'courses.subject.teachers', 'approvedByTeacher']);

        return response()->json(['enrollment' => $enrollment]);
    }

    public function updateStatus(Request $request, WeeklyEnrollment $enrollment): JsonResponse
    {
        $teacher = $request->user()->teacher;
        $this->authorizeAdvisor($request, $enrollment);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'rejection_reason' => ['nullable', 'required_if:status,rejected', 'string', 'max:1000'],
        ]);

        if ($enrollment->status !== 'submitted') {
            return response()->json(['message' => 'อนุมัติได้เฉพาะตารางที่ส่งแล้วเท่านั้น'], 422);
        }

        $enrollment->update([
            'status' => $validated['status'],
            'approved_by_teacher_id' => $teacher->id,
            'approved_at' => now(),
            'rejection_reason' => $validated['status'] === 'rejected'
                ? ($validated['rejection_reason'] ?? null)
                : null,
        ]);

        return response()->json([
            'message' => $validated['status'] === 'approved'
                ? 'อนุมัติตารางเรียนสำเร็จ'
                : 'ปฏิเสธตารางเรียนสำเร็จ',
            'enrollment' => $enrollment->fresh()->load(['student.user', 'courses.subject', 'approvedByTeacher']),
        ]);
    }

    private function authorizeAdvisor(Request $request, WeeklyEnrollment $enrollment): void
    {
        $teacher = $request->user()->teacher;
        $enrollment->loadMissing('student');

        if ((int) $enrollment->student->advisor_teacher_id !== (int) $teacher->id) {
            abort(403, 'อนุมัติได้เฉพาะตารางของนักเรียนในห้องที่ดูแล');
        }
    }
}
