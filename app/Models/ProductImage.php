<?php

namespace App\Models;

use Database\Factories\ProductImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    /** @use HasFactory<ProductImageFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'path',
        'is_primary',
    ];

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * URL pública da imagem: arquivos versionados em public/ ({@see isBundledPublicAsset}) ou disco storage.
     */
    public function publicUrl(): string
    {
        return self::publicUrlForPath($this->path);
    }

    public static function publicUrlForPath(string $path): string
    {
        return self::isBundledPublicAsset($path)
            ? asset($path)
            : asset('storage/' . $path);
    }

    public static function isBundledPublicAsset(string $path): bool
    {
        return str_starts_with($path, 'assets/');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }
}
