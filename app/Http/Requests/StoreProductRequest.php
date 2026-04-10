<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Product::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id'     => ['required', 'exists:categories,id'],
            'name'            => ['required', 'string', 'max:255'],
            'sku'             => ['required', 'string', 'max:100', 'unique:products'],
            'description'     => ['nullable', 'string'],
            'price'           => ['required', 'numeric', 'min:0'],
            'minimum_stock'   => ['required', 'integer', 'min:0'],
            'additional_info' => ['nullable', 'string'],
            'images'          => ['required', 'array', 'min:1'],
            'images.*'        => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }
}
