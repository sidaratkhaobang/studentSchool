<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'student',
        ]);

        Student::create([
            'user_id'            => $user->id,
            'first_name_th'      => $request->first_name_th,
            'last_name_th'       => $request->last_name_th,
            'first_name_en'      => $request->first_name_en,
            'last_name_en'       => $request->last_name_en,
            'date_of_birth'      => $request->date_of_birth,
            'age'                => $request->age,
            'grade_level'        => $request->grade_level,
            'advisor_teacher_id' => $request->advisor_teacher_id,
            'phone'              => $request->phone,
            'email'              => $request->email,
            'status'             => 'pending',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'ลงทะเบียนสำเร็จ กรุณารอการอนุมัติจาก admin',
            'token'   => $token,
            'user'    => [
                'id'       => $user->id,
                'username' => $user->username,
                'email'    => $user->email,
                'role'     => $user->role,
                'status'   => 'pending',
            ],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'], 401);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'บัญชีผู้ใช้ถูกระงับการใช้งาน'], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        $studentStatus = null;
        if ($user->isStudent() && $user->student) {
            $studentStatus = $user->student->status;
        }

        return response()->json([
            'message'        => 'เข้าสู่ระบบสำเร็จ',
            'token'          => $token,
            'token_type'     => 'Bearer',
            'user'           => [
                'id'             => $user->id,
                'username'       => $user->username,
                'email'          => $user->email,
                'role'           => $user->role,
                'student_status' => $studentStatus,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        if ($bearerToken = $request->bearerToken()) {
            PersonalAccessToken::findToken($bearerToken)?->delete();
        }

        $request->user()->tokens()->delete();
        Auth::guard('web')->logout();

        return response()->json(['message' => 'ออกจากระบบสำเร็จ']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('student.advisor');

        return response()->json([
            'user' => $user,
        ]);
    }
}
