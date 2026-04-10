<?php

declare(strict_types = 1);

namespace App\Http\Resources\Api\V1;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ProductImage $model */
        $model = $this->resource;

        return [
            'id'         => $model->id,
            'product_id' => $model->product_id,
            'path'       => $model->path,
            'is_primary' => (bool) $model->is_primary,
            'url'        => $model->publicUrl(),
            'created_at' => $model->created_at?->toIso8601String(),
            'updated_at' => $model->updated_at?->toIso8601String(),
        ];
    }
}
