<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use App\Domain\Inventory\Contracts\ProductQrCodeGenerator;
use App\Models\Product;
use Illuminate\Console\Attributes\{Description, Signature};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

#[Signature('inventory:generate-product-qr-codes {--chunk=100 : Número de produtos por lote (chunkById)} {--only-missing : Gera apenas quando não há caminho ou o arquivo não existe no disco public}')]
#[Description('Gera e grava os QR codes (SVG) de todos os produtos no disco public e atualiza qr_code_path')]
final class GenerateProductQrCodesCommand extends Command
{
    public function handle(ProductQrCodeGenerator $qrCodeGenerator): int
    {
        $this->warnIfPublicStorageLinkIsBroken();

        $chunkSize   = max(1, (int) $this->option('chunk'));
        $onlyMissing = (bool) $this->option('only-missing');

        $processed = 0;
        $skipped   = 0;
        $failures  = 0;

        Product::query()
            ->orderBy('id')
            ->chunkById($chunkSize, function ($products) use ($qrCodeGenerator, $onlyMissing, &$processed, &$skipped, &$failures): void {
                foreach ($products as $product) {
                    if ($onlyMissing && $this->productHasQrFileOnDisk($product)) {
                        $skipped++;

                        continue;
                    }

                    try {
                        $qrCodeGenerator->generateAndStore($product);
                        $processed++;
                    } catch (Throwable $e) {
                        $failures++;
                        $this->components->error("Produto #{$product->id} ({$product->sku}): {$e->getMessage()}");
                    }
                }
            });

        $this->components->info(sprintf(
            'Concluído: %d gerado(s), %d ignorado(s) (only-missing), %d falha(s).',
            $processed,
            $skipped,
            $failures,
        ));

        return $failures > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function warnIfPublicStorageLinkIsBroken(): void
    {
        $publicStorage = public_path('storage');

        if (!file_exists($publicStorage)) {
            $this->components->warn('A pasta ou link public/storage não existe. Execute: php artisan storage:link');

            return;
        }

        if (is_file($publicStorage) && !is_link($publicStorage)) {
            $this->components->warn(
                'public/storage é um arquivo comum (não é symlink). Remova-o e execute: php artisan storage:link — sem isso, QR e imagens em /storage não carregam no navegador.',
            );
        }
    }

    private function productHasQrFileOnDisk(Product $product): bool
    {
        if ($product->qr_code_path === null || $product->qr_code_path === '') {
            return false;
        }

        return Storage::disk('public')->exists($product->qr_code_path);
    }
}
