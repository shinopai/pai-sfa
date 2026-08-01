<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'csv' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:2048',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'csv' => 'CSVファイル',
        ];
    }
}
