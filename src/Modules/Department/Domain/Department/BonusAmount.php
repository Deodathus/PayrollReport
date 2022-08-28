<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Department;

final class BonusAmount
{
    /**
     * @throws BonusAmountException
     */
    public function __construct(public readonly int $amount)
    {
        if ($this->amount <= 0) {
            throw BonusAmountException::amountShouldBeGreaterThanZero();
        }
    }

    public function getNormalized(): float
    {
        return $this->amount / 100;
    }
}
