<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'description',
        'price',
        'minimum_stock',
        'qr_code_path',
        'additional_info',
    ];

    /**
     * @return HasMany<ProductImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<ProductLocation, $this>
     */
    public function locations(): HasMany
    {
        return $this->hasMany(ProductLocation::class);
    }

    /**
     * Produtos com mínimo &gt; 0 cuja soma das quantidades em product_locations é menor que minimum_stock.
     *
     * @param  Builder<Product> $query
     * @return Builder<Product>
     */
    public function scopeWhereTotalStockBelowMinimum(Builder $query): Builder
    {
        $productTable = $query->getModel()->getTable();

        $stockByProduct = ProductLocation::query()
            ->select('product_id')
            ->selectRaw('coalesce(sum(quantity), 0) as stock_total')
            ->groupBy('product_id');

        return $query
            ->where("{$productTable}.minimum_stock", '>', 0)
            ->leftJoinSub($stockByProduct, 'inventory_stock_totals', function ($join) use ($productTable): void {
                $join->on('inventory_stock_totals.product_id', '=', "{$productTable}.id");
            })
            ->where(function (Builder $inner) use ($productTable): void {
                $inner->whereNull('inventory_stock_totals.stock_total')
                    ->orWhereColumn('inventory_stock_totals.stock_total', '<', "{$productTable}.minimum_stock");
            });
    }

    /**
     * Soma das quantidades em todas as posições de estoque.
     */
    public function getTotalStockQuantityAttribute(): int
    {
        if ($this->relationLoaded('locations')) {
            return (int) $this->locations->sum('quantity');
        }

        if (array_key_exists('locations_sum_quantity', $this->attributes)) {
            return (int) $this->attributes['locations_sum_quantity'];
        }

        return (int) $this->locations()->sum('quantity');
    }

    /**
     * Estoque total abaixo do mínimo configurado (mínimo maior que zero).
     */
    public function isBelowMinimumStock(): bool
    {
        if ($this->minimum_stock <= 0) {
            return false;
        }

        return $this->total_stock_quantity < $this->minimum_stock;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price'         => 'decimal:2',
            'minimum_stock' => 'integer',
        ];
    }
}
