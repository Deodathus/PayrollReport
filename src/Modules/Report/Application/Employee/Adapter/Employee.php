<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Employee\Adapter;

final class Employee
{
    public function __construct(
        public readonly string $employeeFirstName,
        public readonly string $employeeLastName,
        public readonly string $departmentName,
        public readonly int $baseSalary,
        public readonly int $salaryBonus,
        public readonly string $salaryBonusType,
        public readonly int $totalSalary
    ) {}
}
