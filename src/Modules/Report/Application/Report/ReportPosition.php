<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Report;

final class ReportPosition
{
    public function __construct(
        public readonly string $employeeFirstName,
        public readonly string $employeeLastName,
        public readonly string $departmentName,
        public readonly float $baseSalary,
        public readonly float $salaryBonus,
        public readonly string $salaryBonusType,
        public readonly float $totalSalary
    ) {}
}
