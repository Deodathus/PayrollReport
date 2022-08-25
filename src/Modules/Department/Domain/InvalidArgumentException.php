<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain;

use Exception;

final class InvalidArgumentException extends Exception
{
    public static function withArgument(string $argument, string $shouldBe): self
    {
        return new self(
            sprintf('Invalid argument: "%s", "%s" expected!', $argument, $shouldBe)
        );
    }
}
