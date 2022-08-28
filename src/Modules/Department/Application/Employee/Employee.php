<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Employee;

final class Employee
{
    public function __construct(
        public readonly string $id,
        public readonly string $departmentId,
        public readonly string $departmentName,
        public readonly string $employeeFirstName,
        public readonly string $employeeLastName,
        public readonly int $baseSalary,
        public readonly int $salaryBonus,
        public readonly string $salaryBonusType,
        public readonly int $totalSalary
    ) {}
}
