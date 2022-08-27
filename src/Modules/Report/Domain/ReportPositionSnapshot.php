<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Domain;

final class ReportPositionSnapshot
{
    public function __construct(
        public readonly string $id,
        public readonly string $reportId,
        public readonly string $employeeFirstName,
        public readonly string $employeeLastName,
        public readonly string $departmentName,
        public readonly int $baseSalary,
        public readonly int $salaryBonus,
        public readonly string $salaryBonusType,
        public readonly int $totalSalary
    ) {}
}
