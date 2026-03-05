<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for uploading a profile photo (multipart/form-data).
 * Expects a single file field named: photo
 */
class UploadProfilePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo' => 'required|file|image|max:2048', // 2MB
        ];
    }

    public function messages(): array
    {
        return [
            'photo.required' => 'photo is required.',
            'photo.image'    => 'photo must be an image file.',
            'photo.max'      => 'photo may not be greater than 2MB.',
        ];
    }
}

