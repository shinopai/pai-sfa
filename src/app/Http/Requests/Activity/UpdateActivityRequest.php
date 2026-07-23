<?php

namespace App\Http\Requests\Activity;

use App\Enums\ActivityType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateActivityRequest extends FormRequest
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
            'activity_type' => [
                'required',
                new Enum(ActivityType::class),
            ],
            'activity_date' => ['required', 'date'],
            'content' => ['required', 'string', 'max:2000'],
        ];
    }
}
