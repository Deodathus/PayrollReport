<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Domain;

final class ReportPosition
{
    public function __construct(
        private readonly ReportPositionId $id,
        private readonly ReportId $reportId,
        private readonly string $employeeFirstName,
        private readonly string $employeeLastName,
        private readonly string $departmentName,
        private readonly int $baseSalary,
        private readonly int $salaryBonus,
        private readonly string $salaryBonusType,
        private readonly int $totalSalary
    ) {}

    public static function create(
        ReportId $reportId,
        string $employeeFirstName,
        string $employeeLastName,
        string $departmentName,
        int $baseSalary,
        int $salaryBonus,
        string $salaryBonusType,
        int $totalSalary
    ): self {
        return new self(
            ReportPositionId::generate(),
            $reportId,
            $employeeFirstName,
            $employeeLastName,
            $departmentName,
            $baseSalary,
            $salaryBonus,
            $salaryBonusType,
            $totalSalary
        );
    }

    public function getSnapshot(): ReportPositionSnapshot
    {
        return new ReportPositionSnapshot(
            $this->id->toString(),
            $this->reportId->toString(),
            $this->employeeFirstName,
            $this->employeeLastName,
            $this->departmentName,
            $this->baseSalary,
            $this->salaryBonus,
            $this->salaryBonusType,
            $this->totalSalary
        );
    }
}
