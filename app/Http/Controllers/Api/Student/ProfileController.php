<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\ProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $student = $request->user()->student->load('advisor', 'user');

        return response()->json(['student' => $student]);
    }

    public function update(ProfileRequest $request): JsonResponse
    {
        $student = $request->user()->student;
        $user    = $request->user();

        $student->update($request->only([
            'first_name_th',
            'last_name_th',
            'first_name_en',
            'last_name_en',
            'date_of_birth',
            'age',
            'grade_level',
            'advisor_teacher_id',
            'phone',
            'email',
        ]));

        if ($request->filled('new_password')) {
            $request->validate([
                'current_password' => 'required',
                'new_password'     => 'required|min:8|confirmed',
            ]);

            if (! Hash::check($request->current_password, $user->password)) {
                return response()->json(['message' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง'], 422);
            }

            $user->update(['password' => $request->new_password]);
        }

        if ($request->filled('email')) {
            $user->update(['email' => $request->email]);
        }

        return response()->json([
            'message' => 'อัปเดตข้อมูลส่วนตัวสำเร็จ',
            'student' => $student->fresh()->load('advisor'),
        ]);
    }
}
