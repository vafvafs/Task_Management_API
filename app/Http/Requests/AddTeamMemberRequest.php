<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class AddTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'user_id is required.',
            'user_id.exists'   => 'User not found.',
        ];
    }
}
