<?php

namespace App\Http\Requests\Deal;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\DealStatus;
use Illuminate\Validation\Rules\Enum;

class StoreDealRequest extends FormRequest
{
    /**
     * このリクエストを許可するか
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:120'],
            'amount' => ['nullable', 'integer', 'min:0'],
            'status' => [
                'required',
                new Enum(DealStatus::class),
            ],
            'expected_contract_date' => ['nullable', 'date'],
            'memo' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
