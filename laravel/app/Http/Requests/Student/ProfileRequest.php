<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->user()->student?->id;
        $userId    = $this->user()->id;

        return [
            'first_name_th'      => ['sometimes', 'string', 'max:100'],
            'last_name_th'       => ['sometimes', 'string', 'max:100'],
            'first_name_en'      => ['sometimes', 'string', 'max:100'],
            'last_name_en'       => ['sometimes', 'string', 'max:100'],
            'date_of_birth'      => ['sometimes', 'date', 'before:today'],
            'age'                => ['sometimes', 'integer', 'min:5', 'max:99'],
            'grade_level'        => ['sometimes', 'string', 'max:20'],
            'advisor_teacher_id' => ['nullable', 'exists:teachers,id'],
            'phone'              => ['nullable', 'string', 'max:20'],
            'email'              => ['sometimes', 'email', 'max:100', Rule::unique('users', 'email')->ignore($userId)],
        ];
    }
}
