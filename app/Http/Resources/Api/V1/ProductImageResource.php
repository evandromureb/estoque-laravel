<?php

declare(strict_types = 1);

namespace App\Http\Resources\Api\V1;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ProductImage $model */
        $model = $this->resource;

        $data = parent::toArray($request);
        assert(is_array($data));

        return array_merge($data, [
            'url' => $model->publicUrl(),
        ]);
    }
}
