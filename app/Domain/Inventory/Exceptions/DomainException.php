<?php

declare(strict_types = 1);

namespace App\Domain\Inventory\Exceptions;

use RuntimeException;

/**
 * Base exception for inventory domain rule violations.
 */
abstract class DomainException extends RuntimeException
{
}
