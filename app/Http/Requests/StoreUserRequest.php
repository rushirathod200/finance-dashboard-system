<?php

namespace App\Http\Requests;

use App\Http\Requests\Api\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(User::roles())],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
