<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Report\Generate;

use DateTimeImmutable;
use PayrollReport\Modules\Report\Application\Employee\Adapter\Employee;
use PayrollReport\Modules\Report\Application\Employee\Adapter\EmployeeFetcherInterface;
use PayrollReport\Modules\Report\Domain\Report;
use PayrollReport\Modules\Report\Domain\ReportId;
use PayrollReport\Modules\Report\Domain\ReportPosition;
use PayrollReport\Modules\Report\Domain\ReportPositions;
use PayrollReport\Modules\Report\Domain\ReportRepository;
use PayrollReport\Shared\Application\Command\CommandHandler;
use PayrollReport\Shared\Application\NotFoundException;

final class GenerateReportHandler implements CommandHandler
{
    public function __construct(
        private readonly EmployeeFetcherInterface $employeeFetcher,
        private readonly ReportRepository $reportRepository
    ) {}

    /**
     * @throws NotFoundException
     */
    public function __invoke(GenerateReportCommand $generateReportCommand): void
    {
        $employees = $this->employeeFetcher->fetchAll();

        if (count($employees) === 0) {
            throw NotFoundException::notFound();
        }

        $reportId = ReportId::generate();
        $reportPositions = $this->prepareReportPositionsForReport($reportId, $employees);

        $report = new Report(
            $reportId,
            new ReportPositions(
                ...$reportPositions
            ),
            new DateTimeImmutable()
        );

        $this->reportRepository->store($report);
    }

    private function prepareReportPositionsForReport(ReportId $reportId, array $employees): array
    {
        return array_map(
            static function (Employee $employee) use ($reportId): ReportPosition {
                return ReportPosition::create(
                    $reportId,
                    $employee->employeeFirstName,
                    $employee->employeeLastName,
                    $employee->departmentName,
                    $employee->baseSalary,
                    $employee->salaryBonus,
                    $employee->salaryBonusType,
                    $employee->totalSalary
                );
            },
            $employees
        );
    }
}
