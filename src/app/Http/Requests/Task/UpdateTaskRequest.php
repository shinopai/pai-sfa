<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TaskPriority;
use Illuminate\Validation\Rules\Enum;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'deal_id' => ['required', 'exists:deals,id'],
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'due_date' => ['required', 'date'],
            'priority' => [
                'required',
                new Enum(TaskPriority::class),
            ],
            'is_completed' => ['required', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'deal_id' => '商談',
            'title' => 'タスク名',
            'description' => 'タスク詳細',
            'due_date' => '期限日',
            'priority' => '優先度',
            'is_completed' => '完了',
        ];
    }
}
