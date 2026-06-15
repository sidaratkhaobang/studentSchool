<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacherId = $this->route('teacher')?->id;

        return [
            'first_name_th' => ['required', 'string', 'max:100'],
            'last_name_th'  => ['required', 'string', 'max:100'],
            'first_name_en' => ['required', 'string', 'max:100'],
            'last_name_en'  => ['required', 'string', 'max:100'],
            'email'         => ['required', 'email', 'max:100', Rule::unique('teachers', 'email')->ignore($teacherId)],
            'phone'         => ['nullable', 'string', 'max:20'],
            'bio'           => ['nullable', 'string'],
            'is_active'     => ['sometimes', 'boolean'],
        ];
    }
}
