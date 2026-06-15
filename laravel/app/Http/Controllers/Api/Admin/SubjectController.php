<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubjectRequest;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Subject::withCount('teachers');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_th', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('subject_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $subjects = $query->with(['teachers' => function ($q) {
            $q->select('teachers.id', 'first_name_th', 'last_name_th', 'is_primary')
                ->orderByPivot('is_primary', 'desc');
        }])
            ->orderBy('subject_code')
            ->paginate($request->integer('per_page', 15));

        return response()->json($subjects);
    }

    public function store(SubjectRequest $request): JsonResponse
    {
        $subject = Subject::create($request->validated());

        return response()->json([
            'message' => 'เพิ่มรายวิชาสำเร็จ',
            'subject' => $subject,
        ], 201);
    }

    public function show(Subject $subject): JsonResponse
    {
        $subject->load('teachers');

        return response()->json(['subject' => $subject]);
    }

    public function update(SubjectRequest $request, Subject $subject): JsonResponse
    {
        $subject->update($request->validated());

        return response()->json([
            'message' => 'แก้ไขรายวิชาสำเร็จ',
            'subject' => $subject->fresh()->load('teachers'),
        ]);
    }

    public function destroy(Subject $subject): JsonResponse
    {
        if ($subject->enrollmentCourses()->exists()) {
            return response()->json([
                'message' => 'ไม่สามารถลบได้ เนื่องจากมีนักเรียนลงทะเบียนรายวิชานี้แล้ว',
            ], 422);
        }

        $subject->delete();

        return response()->json(['message' => 'ลบรายวิชาสำเร็จ']);
    }
}
