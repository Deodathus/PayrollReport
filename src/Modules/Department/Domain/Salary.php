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

    /**
     * @throws InvalidArgumentException
     */
    public function getNormalized(): float
    {
        if ($this->amount < 100) {
            throw InvalidArgumentException::withArgument((string) $this->amount, 'equal or greater than 100');
        }

        return $this->amount / 100;
    }
}
