<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driven\Report;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PayrollReport\Modules\Report\Domain\ReportPositionRepository;
use PayrollReport\Modules\Report\Domain\ReportPositionSnapshot;

final class ReportPositionDbRepository implements ReportPositionRepository
{
    private const DB_TABLE_NAME = 'reports_positions';

    public function __construct(private readonly Connection $connection) {}

    /**
     * @throws Exception
     */
    public function store(ReportPositionSnapshot $reportPositions): void
    {
        $this->connection
            ->createQueryBuilder()
            ->insert(self::DB_TABLE_NAME)
            ->values([
                'id' => ':reportPositionId',
                'report_id' => ':reportId',
                'employee_first_name' => ':employeeFirstName',
                'employee_last_name' => ':employeeLastName',
                'department_name' => ':departmentName',
                'base_salary' => ':baseSalary',
                'salary_bonus' => ':salaryBonus',
                'salary_bonus_type' => ':salaryBonusType',
                'total_salary' => ':totalSalary',
            ])
            ->setParameters([
                'reportPositionId' => $reportPositions->id,
                'reportId' => $reportPositions->reportId,
                'employeeFirstName' => $reportPositions->employeeFirstName,
                'employeeLastName' => $reportPositions->employeeLastName,
                'departmentName' => $reportPositions->departmentName,
                'baseSalary' => $reportPositions->baseSalary,
                'salaryBonus' => $reportPositions->salaryBonus,
                'salaryBonusType' => $reportPositions->salaryBonusType,
                'totalSalary' => $reportPositions->totalSalary,
            ])
            ->executeStatement();
    }
}
