<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Report\Generate;

use DateTimeImmutable;
use PayrollReport\Modules\Department\Domain\Department\DepartmentRepository;
use PayrollReport\Modules\Department\Domain\Employee\Employee;
use PayrollReport\Modules\Department\Domain\Employee\EmployeeRepository;
use PayrollReport\Modules\Department\Domain\Employee\Policy\SalaryWithDepartmentBonusPolicy;
use PayrollReport\Modules\Report\Domain\Report;
use PayrollReport\Modules\Report\Domain\ReportId;
use PayrollReport\Modules\Report\Domain\ReportPosition;
use PayrollReport\Modules\Report\Domain\ReportPositions;
use PayrollReport\Modules\Report\Domain\ReportRepository;
use PayrollReport\Shared\Application\Command\CommandHandler;

final class GenerateReportHandler implements CommandHandler
{
    public function __construct(
        private readonly DepartmentRepository            $departmentRepository,
        private readonly EmployeeRepository              $employeeRepository,
        private readonly SalaryWithDepartmentBonusPolicy $salaryWithDepartmentBonusPolicy,
        private readonly ReportRepository                $reportRepository
    ) {}

    public function __invoke(GenerateReportCommand $generateReportCommand): void
    {
        $departmentsNames = $this->departmentRepository->fetchNames();
        $employees = $this->employeeRepository->fetchAll();

        $reportId = ReportId::generate();

        $reportPositions = $this->prepareReportPositionsForReport($departmentsNames, $reportId, $employees);

        $report = new Report(
            $reportId,
            new ReportPositions(
                ...$reportPositions
            ),
            new DateTimeImmutable()
        );

        $this->reportRepository->store($report);
    }

    private function prepareReportPositionsForReport(array $departmentsNames, ReportId $reportId, array $employees): array
    {
        return array_map(
            function (Employee $employee) use ($departmentsNames, $reportId): ReportPosition {
                $employeeSnapshot = $employee->getSnapshot();

                $totalSalary = $employee->getSalaryWithDepartmentBonus($this->salaryWithDepartmentBonusPolicy);

                $salary = $employeeSnapshot->salary;
                $salaryBonus = $totalSalary->salary->amount - $salary;

                return ReportPosition::create(
                    $reportId,
                    $employeeSnapshot->firstName,
                    $employeeSnapshot->lastName,
                    $departmentsNames[$employeeSnapshot->departmentId]['name'],
                    $employeeSnapshot->salary,
                    $salaryBonus,
                    $totalSalary->departmentSalaryBonusType->value,
                    $totalSalary->salary->amount
                );
            },
            $employees
        );
    }
}
