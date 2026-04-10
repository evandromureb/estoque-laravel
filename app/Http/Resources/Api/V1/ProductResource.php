<?php

declare(strict_types = 1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'category_id'          => $this->category_id,
            'name'                 => $this->name,
            'sku'                  => $this->sku,
            'description'          => $this->description,
            'price'                => $this->price,
            'minimum_stock'        => $this->minimum_stock,
            'qr_code_path'         => $this->qr_code_path,
            'additional_info'      => $this->additional_info,
            'created_at'           => $this->created_at?->toIso8601String(),
            'updated_at'           => $this->updated_at?->toIso8601String(),
            'category'             => CategoryResource::make($this->whenLoaded('category')),
            'images'               => ProductImageResource::collection($this->whenLoaded('images')),
            'locations'            => ProductLocationResource::collection($this->whenLoaded('locations')),
            'total_stock_quantity' => $this->when(
                $this->relationLoaded('locations') || array_key_exists('locations_sum_quantity', $this->getAttributes()),
                (int) $this->total_stock_quantity,
            ),
        ];
    }
}
