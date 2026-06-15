<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id'  => ['required', 'exists:subjects,id'],
            'day_of_week' => ['required', Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])],
            'hours'       => ['required', 'numeric', 'min:0.5', 'max:6'],
            'start_time'  => ['nullable', 'date_format:H:i'],
            'end_time'    => ['nullable', 'date_format:H:i', 'after:start_time'],
        ];
    }

    public function messages(): array
    {
        return [
            'subject_id.required'  => 'กรุณาเลือกรายวิชา',
            'subject_id.exists'    => 'รายวิชาที่เลือกไม่ถูกต้อง',
            'day_of_week.required' => 'กรุณาเลือกวัน',
            'day_of_week.in'       => 'วันที่เลือกไม่ถูกต้อง',
            'hours.required'       => 'กรุณากรอกจำนวนชั่วโมง',
            'hours.min'            => 'จำนวนชั่วโมงน้อยที่สุดคือ 0.5 ชั่วโมง',
            'hours.max'            => 'จำนวนชั่วโมงสูงสุดต่อวิชาคือ 6 ชั่วโมง',
        ];
    }
}
