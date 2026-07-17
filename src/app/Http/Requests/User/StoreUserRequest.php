<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:admin,sales'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    // public function attributes(): array
    // {
    //     return [
    //         'name' => '氏名',
    //         'email' => 'メールアドレス',
    //         'role' => '権限',
    //         'password' => 'パスワード',
    //     ];
    // }
}
