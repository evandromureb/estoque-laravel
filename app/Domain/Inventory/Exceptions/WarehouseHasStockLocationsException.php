<?php

declare(strict_types = 1);

namespace App\Domain\Inventory\Exceptions;

final class WarehouseHasStockLocationsException extends DomainException
{
    public static function make(): self
    {
        return new self('Não é possível remover armazém com estoque vinculado.');
    }
}
