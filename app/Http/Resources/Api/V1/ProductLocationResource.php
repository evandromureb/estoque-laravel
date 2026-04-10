<?php

declare(strict_types = 1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductLocationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'product_id'   => $this->product_id,
            'warehouse_id' => $this->warehouse_id,
            'aisle'        => $this->aisle,
            'shelf'        => $this->shelf,
            'quantity'     => $this->quantity,
            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),
            'warehouse'    => WarehouseResource::make($this->whenLoaded('warehouse')),
            'product'      => ProductResource::make($this->whenLoaded('product')),
        ];
    }
}
