<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Department;

final class Department
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $salaryBonusType,
        public readonly float $salaryBonus
    ) {}
}
