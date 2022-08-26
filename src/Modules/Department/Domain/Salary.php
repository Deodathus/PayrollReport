<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain;

final class Salary
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(public readonly int $amount)
    {
        if ($this->amount <= 0) {
            throw InvalidArgumentException::withArgument((string) $this->amount, 'greater than 0');
        }
    }

    public function getNormalized(): float
    {
        return $this->amount / 100;
    }
}
