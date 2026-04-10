<?php

declare(strict_types = 1);

namespace App\Http\Requests\Api\V1;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User $user */
        $user = $this->route('user');

        return $this->user()?->can('update', $user) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'        => ['nullable', 'string', 'min:8'],
            'additional_info' => ['nullable', 'string'],
            'is_admin'        => ['sometimes', 'boolean'],
        ];
    }
}
