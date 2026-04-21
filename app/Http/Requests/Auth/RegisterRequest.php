<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username'           => ['required', 'string', 'min:4', 'max:20', 'unique:users,username', 'regex:/^[a-z0-9_]+$/'],
            'email'              => ['required', 'email', 'max:100', 'unique:users,email'],
            'password'           => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'first_name_th'      => ['required', 'string', 'max:100'],
            'last_name_th'       => ['required', 'string', 'max:100'],
            'first_name_en'      => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'last_name_en'       => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'date_of_birth'      => ['required', 'date', 'before:today'],
            'age'                => ['required', 'integer', 'min:5', 'max:99'],
            'grade_level'        => ['required', 'string', 'max:20'],
            'advisor_teacher_id' => ['nullable', 'exists:teachers,id'],
            'phone'              => ['nullable', 'string', 'max:20', 'regex:/^[0-9\-\+\(\)\s]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required'      => 'กรุณากรอกชื่อผู้ใช้',
            'username.min'           => 'ชื่อผู้ใช้ต้องมีอย่างน้อย 4 ตัวอักษร',
            'username.unique'        => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว',
            'username.regex'         => 'ชื่อผู้ใช้ใช้ได้เฉพาะ a-z, 0-9 และ underscore',
            'email.required'         => 'กรุณากรอกอีเมล',
            'email.unique'           => 'อีเมลนี้ถูกใช้งานแล้ว',
            'password.required'      => 'กรุณากรอกรหัสผ่าน',
            'password.min'           => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
            'password.regex'         => 'รหัสผ่านต้องมีตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก และตัวเลข',
            'date_of_birth.required' => 'กรุณากรอกวันเกิด',
            'date_of_birth.before'   => 'วันเกิดต้องเป็นวันที่ผ่านมาแล้ว',
        ];
    }
}
