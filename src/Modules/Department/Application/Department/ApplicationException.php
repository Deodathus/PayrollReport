<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Department;

use Exception;
use PayrollReport\Modules\Department\Domain\Department\DomainException;

final class ApplicationException extends Exception
{
    public static function fromDomainException(DomainException $exception): self
    {
        return new self($exception->getMessage(), $exception->getCode(), $exception);
    }
}
