<?php

namespace App\Http\Requests;

use App\Models\Warehouse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Warehouse $warehouse */
        $warehouse = $this->route('warehouse');

        return $this->user()?->can('update', $warehouse) ?? false;
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
