<?php
declare(strict_types=1);

namespace PayrollReport\Shared\Application;

use Exception;

final class NotFoundException extends Exception
{
    public static function notFound(): self
    {
        return new self('Nothing was found!');
    }

    public static function notFoundById(string $id): self
    {
        return new self(sprintf('Resource not found by id: "%s"', $id));
    }

    public static function notFoundByName(string $name): self
    {
        return new self(sprintf('Resource not found by name: "%s"', $name));
    }
}
