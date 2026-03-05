<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for POST /import/teams (multipart/form-data).
 * Expects a single file field named: file
 */
class ImportTeamsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'file is required.',
            'file.mimes'    => 'file must be an Excel file (xlsx, xls) or csv.',
            'file.max'      => 'file may not be greater than 10MB.',
        ];
    }
}

