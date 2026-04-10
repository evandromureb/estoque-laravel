<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductImagesRequest extends FormRequest
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
        return [
            'images'   => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }
}
