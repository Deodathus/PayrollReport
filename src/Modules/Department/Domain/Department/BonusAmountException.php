<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Department;

final class BonusAmountException extends DomainException
{
    public static function amountShouldBeGreaterThanZero(): self
    {
        return new self('Amount should be greater than 0!');
    }
}
