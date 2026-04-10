<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DestroyUserApiTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User $user */
        $user = $this->route('user');

        return $this->user()?->can('revokeApiToken', $user) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}
