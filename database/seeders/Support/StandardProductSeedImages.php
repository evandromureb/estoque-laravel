<?php

namespace Database\Seeders\Support;

use Illuminate\Support\Facades\File;
use RuntimeException;

class StandardProductSeedImages
{
    /**
     * Caminhos relativos à pasta public/ (servidos com asset()).
     */
    public const string PUBLIC_PREFIX = 'assets/images/products';

    /**
     * Arquivos versionados em public/assets/images/products (demonstração).
     *
     * @return list<string>
     */
    public static function relativePaths(): array
    {
        return [
            self::PUBLIC_PREFIX . '/demo-1.png',
            self::PUBLIC_PREFIX . '/demo-2.png',
            self::PUBLIC_PREFIX . '/demo-3.png',
            self::PUBLIC_PREFIX . '/demo-4.png',
        ];
    }

    /**
     * Garante que as imagens de demonstração existem em public/ (commitadas no repositório).
     */
    public static function assertBundledFilesPresent(): void
    {
        foreach (self::relativePaths() as $relative) {
            $full = public_path($relative);

            if (!File::exists($full)) {
                throw new RuntimeException("Imagem de demonstração ausente em public: {$relative}");
            }
        }
    }

    /**
     * @return list<string>
     */
    public static function pickPathsForProduct(int $productIndex, int $imageCount): array
    {
        $all   = self::relativePaths();
        $paths = [];
        $n     = count($all);

        for ($i = 0; $i < $imageCount; $i++) {
            $paths[] = $all[($productIndex + $i) % $n];
        }

        return $paths;
    }
}
