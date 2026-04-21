<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Student::with(['user', 'advisor'])
            ->withCount('weeklyEnrollments');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name_th', 'like', "%{$search}%")
                    ->orWhere('last_name_th', 'like', "%{$search}%")
                    ->orWhere('first_name_en', 'like', "%{$search}%")
                    ->orWhere('last_name_en', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q) => $q->where('username', 'like', "%{$search}%"));
            });
        }

        $students = $query->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json($students);
    }

    public function show(Student $student): JsonResponse
    {
        $student->load(['user', 'advisor', 'weeklyEnrollments.courses.subject']);

        return response()->json(['student' => $student]);
    }

    public function updateStatus(Request $request, Student $student): JsonResponse
    {
        $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected', 'pending'])],
        ]);

        $student->update(['status' => $request->status]);

        if ($request->status === 'approved') {
            $student->user->update(['is_active' => true]);
        }

        return response()->json([
            'message' => 'อัปเดตสถานะนักเรียนสำเร็จ',
            'student' => $student->fresh()->load('user'),
        ]);
    }
}
