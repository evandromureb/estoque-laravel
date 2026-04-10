<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Product $product */
        $product = $this->route('product');

        return $this->user()?->can('update', $product) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Product $product */
        $product = $this->route('product');

        return [
            'category_id'     => ['required', 'exists:categories,id'],
            'name'            => ['required', 'string', 'max:255'],
            'sku'             => ['required', 'string', 'max:100', 'unique:products,sku,' . $product->id],
            'description'     => ['nullable', 'string'],
            'price'           => ['required', 'numeric', 'min:0'],
            'minimum_stock'   => ['required', 'integer', 'min:0'],
            'additional_info' => ['nullable', 'string'],
            'images'          => ['nullable', 'array'],
            'images.*'        => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }
}
