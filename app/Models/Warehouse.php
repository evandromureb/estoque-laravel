<?php

namespace App\Models;

use Database\Factories\WarehouseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    /** @use HasFactory<WarehouseFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'location_string',
        'description',
        'additional_info',
    ];

    /**
     * @return HasMany<ProductLocation, $this>
     */
    public function productLocations(): HasMany
    {
        return $this->hasMany(ProductLocation::class);
    }
}
