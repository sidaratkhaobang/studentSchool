<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TeacherRequest;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Teacher::withCount('advisingStudents', 'subjects');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name_th', 'like', "%{$search}%")
                    ->orWhere('last_name_th', 'like', "%{$search}%")
                    ->orWhere('first_name_en', 'like', "%{$search}%")
                    ->orWhere('last_name_en', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $teachers = $query->orderBy('first_name_th')
            ->paginate($request->integer('per_page', 15));

        return response()->json($teachers);
    }

    public function store(TeacherRequest $request): JsonResponse
    {
        $teacher = Teacher::create($request->validated());

        return response()->json([
            'message' => 'เพิ่มอาจารย์สำเร็จ',
            'teacher' => $teacher,
        ], 201);
    }

    public function show(Teacher $teacher): JsonResponse
    {
        $teacher->load('subjects.subjectTeachers', 'advisingStudents');

        return response()->json(['teacher' => $teacher]);
    }

    public function update(TeacherRequest $request, Teacher $teacher): JsonResponse
    {
        $teacher->update($request->validated());

        return response()->json([
            'message' => 'แก้ไขข้อมูลอาจารย์สำเร็จ',
            'teacher' => $teacher->fresh(),
        ]);
    }

    public function destroy(Teacher $teacher): JsonResponse
    {
        if ($teacher->advisingStudents()->count() > 0) {
            return response()->json([
                'message' => 'ไม่สามารถลบได้ เนื่องจากอาจารย์ท่านนี้เป็นที่ปรึกษาของนักเรียน',
            ], 422);
        }

        $teacher->delete();

        return response()->json(['message' => 'ลบอาจารย์สำเร็จ']);
    }
}
