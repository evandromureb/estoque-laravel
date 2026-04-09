<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreUserApiTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User $user */
        $user = $this->route('user');

        return $this->user()?->can('createApiToken', $user) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token_name' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $tokenName = (string) $this->input('token_name');

            if ($tokenName !== User::DEFAULT_API_TOKEN_NAME) {
                return;
            }

            /** @var User $user */
            $user = $this->route('user');

            if ($user->tokens()->where('name', User::DEFAULT_API_TOKEN_NAME)->exists()) {
                $validator->errors()->add(
                    'token_name',
                    'Este usuário já possui um token com o nome padrão. Revogue ou exclua o token existente antes de gerar outro.',
                );
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $name = $this->string('token_name')->trim()->toString();

        if ($name === '') {
            $this->merge(['token_name' => User::DEFAULT_API_TOKEN_NAME]);
        }
    }
}
