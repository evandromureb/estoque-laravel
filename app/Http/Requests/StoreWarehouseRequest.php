<?php

namespace App\Http\Requests;

use App\Models\Warehouse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Warehouse::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'location_string' => ['nullable', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'additional_info' => ['nullable', 'string'],
        ];
    }
}
