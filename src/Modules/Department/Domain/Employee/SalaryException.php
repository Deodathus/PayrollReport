<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Employee;

final class SalaryException extends DomainException
{
    public static function amountShouldBeGreaterThanZero(): self
    {
        return new self('Amount should be greater than 0!');
    }
}
