<?php

namespace App\Http\Requests;

use App\Models\ProductLocation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', ProductLocation::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id'   => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'aisle'        => ['nullable', 'string', 'max:50'],
            'shelf'        => ['nullable', 'string', 'max:50'],
            'quantity'     => ['required', 'integer', 'min:1'],
        ];
    }
}
