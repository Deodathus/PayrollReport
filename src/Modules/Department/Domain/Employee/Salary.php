<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Employee;

final class Salary
{
    /**
     * @throws SalaryException
     */
    public function __construct(public readonly int $amount)
    {
        if ($this->amount <= 0) {
            throw SalaryException::amountShouldBeGreaterThanZero();
        }
    }

    public function getNormalized(): float
    {
        return $this->amount / 100;
    }
}
