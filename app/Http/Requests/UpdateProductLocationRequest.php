<?php

namespace App\Http\Requests;

use App\Models\ProductLocation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProductLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var ProductLocation|null $location */
        $location = $this->route('product_location');

        return $location !== null && ($this->user()?->can('update', $location) ?? false);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'aisle'        => ['nullable', 'string', 'max:50'],
            'shelf'        => ['nullable', 'string', 'max:50'],
            'quantity'     => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var ProductLocation $location */
            $location = $this->route('product_location');

            $duplicate = ProductLocation::query()
                ->where('product_id', $location->product_id)
                ->where('warehouse_id', $this->input('warehouse_id'))
                ->where('aisle', $this->input('aisle'))
                ->where('shelf', $this->input('shelf'))
                ->where('id', '!=', $location->id)
                ->exists();

            if ($duplicate) {
                $validator->errors()->add(
                    'warehouse_id',
                    'Já existe outra posição com o mesmo armazém e corredor/prateleira para este produto.'
                );
            }
        });
    }
}
