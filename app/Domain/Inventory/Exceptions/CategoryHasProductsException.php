<?php

declare(strict_types = 1);

namespace App\Domain\Inventory\Exceptions;

final class CategoryHasProductsException extends DomainException
{
    public static function make(): self
    {
        return new self('Não é possível remover categoria que possui produtos vinculados.');
    }
}
