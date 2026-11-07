<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubjectContentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user()->teacher;

        $subjects = $teacher->subjects()
            ->withCount('enrollmentCourses')
            ->orderBy('subject_code')
            ->get();

        return response()->json(['subjects' => $subjects]);
    }

    public function update(Request $request, Subject $subject): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (! $subject->teachers()->where('teachers.id', $teacher->id)->exists()) {
            abort(403, 'จัดการได้เฉพาะรายวิชาที่รับผิดชอบ');
        }

        $validated = $request->validate([
            'learning_content' => ['nullable', 'string', 'max:10000'],
            'material_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip', 'max:10240'],
        ]);

        $payload = [
            'learning_content' => $validated['learning_content'] ?? null,
        ];

        if ($request->hasFile('material_file')) {
            if ($subject->material_path) {
                Storage::disk('public')->delete($subject->material_path);
            }

            $payload['material_path'] = $request->file('material_file')->store('subject-materials', 'public');
        }

        $subject->update($payload);

        return response()->json([
            'message' => 'อัปเดตเนื้อหาและเอกสารรายวิชาสำเร็จ',
            'subject' => $subject->fresh()->load('teachers'),
        ]);
    }
}
