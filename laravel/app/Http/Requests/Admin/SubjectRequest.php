<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subjectId = $this->route('subject')?->id;

        return [
            'subject_code'     => ['required', 'string', 'max:20', Rule::unique('subjects', 'subject_code')->ignore($subjectId)],
            'name_th'          => ['required', 'string', 'max:150'],
            'name_en'          => ['required', 'string', 'max:150'],
            'description'      => ['nullable', 'string'],
            'credit_hours'     => ['required', 'integer', 'min:1', 'max:10'],
            'hours_per_session' => ['required', 'integer', 'min:1', 'max:6'],
            'is_active'        => ['sometimes', 'boolean'],
        ];
    }
}
