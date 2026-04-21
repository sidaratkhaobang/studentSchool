<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubjectTeacherRequest;
use App\Models\SubjectTeacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectTeacherController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SubjectTeacher::with(['subject', 'teacher'])
            ->orderBy('subject_id');

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        $assignments = $query->paginate($request->integer('per_page', 15));

        return response()->json($assignments);
    }

    public function store(SubjectTeacherRequest $request): JsonResponse
    {
        $exists = SubjectTeacher::where('subject_id', $request->subject_id)
            ->where('teacher_id', $request->teacher_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'อาจารย์ท่านนี้ผูกกับรายวิชานี้อยู่แล้ว'], 422);
        }

        if ($request->boolean('is_primary')) {
            SubjectTeacher::where('subject_id', $request->subject_id)
                ->update(['is_primary' => false]);
        }

        $assignment = SubjectTeacher::create($request->validated());
        $assignment->load(['subject', 'teacher']);

        return response()->json([
            'message'    => 'ผูกรายวิชากับอาจารย์สำเร็จ',
            'assignment' => $assignment,
        ], 201);
    }

    public function update(SubjectTeacherRequest $request, SubjectTeacher $subjectTeacher): JsonResponse
    {
        if ($request->boolean('is_primary')) {
            SubjectTeacher::where('subject_id', $subjectTeacher->subject_id)
                ->where('id', '!=', $subjectTeacher->id)
                ->update(['is_primary' => false]);
        }

        $subjectTeacher->update($request->only('is_primary'));

        return response()->json([
            'message'    => 'อัปเดตสำเร็จ',
            'assignment' => $subjectTeacher->fresh()->load(['subject', 'teacher']),
        ]);
    }

    public function destroy(SubjectTeacher $subjectTeacher): JsonResponse
    {
        $subjectTeacher->delete();

        return response()->json(['message' => 'ยกเลิกการผูกรายวิชาสำเร็จ']);
    }
}
