<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;


class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $routeUser = $this->route('user');
        $userId = $routeUser?->id ?? null;
        $role = $this->input('role') ?? $routeUser?->role ?? User::ROLE_USER;
        $rules = [
            'name'  => 'sometimes|string|min:2|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $userId,
        ];
        if ($this->user()->isAdmin()) {
            if ($role === User::ROLE_ADMIN) {
                $rules['role'] = 'sometimes|in:'.User::ROLE_ADMIN;
                $rules['team_id'] = 'prohibited';
                $rules['team_name'] = 'prohibited';
            } elseif ($role === User::ROLE_TEAM_LEADER) {
                $rules['role'] = 'sometimes|in:'.User::ROLE_TEAM_LEADER;
                $rules['team_id'] = 'nullable|integer|exists:teams,id';
                $rules['team_name'] = 'nullable|string|min:2|max:255';
            } else {
                $rules['role'] = 'sometimes|in:'.User::ROLE_USER;
                $rules['team_id'] = 'nullable|integer|exists:teams,id';
            }
        } else {
            $rules['role'] = 'prohibited';
            $rules['team_id'] = 'prohibited';
            $rules['team_name'] = 'prohibited';
        }
        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.string'       => 'Name must be a string.',
            'name.min'          => 'Name must be at least 2 characters.',
            'name.max'          => 'Name cannot exceed 255 characters.',
            'name.regex'        => 'Name can only contain letters and spaces.',
            'email.string'      => 'Email must be a string.',
            'email.email'       => 'Please provide a valid email address.',
            'email.max'         => 'Email cannot exceed 255 characters.',
            'email.unique'      => 'This email is already registered.',
            'team_id.integer'   => 'Team ID must be a valid integer.',
            'team_id.exists'    => 'The selected team does not exist.',
            'team_name.min'     => 'Team name must be at least 2 characters.',
            'team_name.max'     => 'Team name cannot exceed 255 characters.',
        ];
    }
}
