<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class TaskUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $taskId = $this->route('task')?->id ?? null;
        return [
            'title'       => 'sometimes|required|string|min:3|max:255|unique:tasks,title,' . $taskId,
            'description' => 'nullable|string|max:1000',
            'completed'   => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'Title is required.',
            'title.string'      => 'Title must be a string.',
            'title.min'         => 'Title must be at least 3 characters.',
            'title.max'         => 'Title cannot exceed 255 characters.',
            'title.unique'      => 'Title already exist',
            'description.string' => 'Description must be a string.',
            'description.max'   => 'Description cannot exceed 1000 characters.',
            'completed.boolean'  => 'Completed must be true or false.',
        ];
    }
}
