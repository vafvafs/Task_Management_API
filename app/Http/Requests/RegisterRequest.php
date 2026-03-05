<?php

namespace App\Http\Requests;

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
            'name'     => 'required|string|min:2|max:255|regex:/^[a-zA-Z\s]+$/',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Name is required.',
            'name.string'       => 'Name must be a string.',
            'name.min'          => 'Name must be at least 2 characters.',
            'name.max'          => 'Name cannot exceed 255 characters.',
            'name.regex'        => 'Name can only contain letters and spaces.',
            'email.required'    => 'Email is required.',
            'email.email'       => 'Please provide a valid email address.',
            'email.max'         => 'Email cannot exceed 255 characters.',
            'email.unique'      => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.min'      => 'Password must be at least 8 characters.',
            'password.max'      => 'Password cannot exceed 255 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.regex'    => 'Password must contain at least one uppercase, lowercase, number, and special character.',
        ];
    }

    /**
     * Normalize name/email before controller receives them. When $key is passed, return parent result (avoid treating string as array).
     */
    public function validated($key = null, $default = null): array
    {
        $data = parent::validated($key, $default);
        if ($key !== null) {
            return $data;
        }
        $data['name']  = trim($data['name']);
        $data['email'] = strtolower(trim($data['email']));
        return $data;
    }
}
