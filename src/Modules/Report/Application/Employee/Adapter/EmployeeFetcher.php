<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Employee\Adapter;

use PayrollReport\Modules\Department\Application\Employee\Employee as EmployeeModel;
use PayrollReport\Modules\Department\Application\Employee\EmployeeFetcher as ExternalEmployeeFetcher;

final class EmployeeFetcher implements EmployeeFetcherInterface
{
    public function __construct(private readonly ExternalEmployeeFetcher $employeeFetcher) {}

    /**
     * @return Employee[]
     */
    public function fetchAll(): array
    {
        return array_map(
            static fn(EmployeeModel $employee): Employee => new Employee(
                $employee->employeeFirstName,
                $employee->employeeLastName,
                $employee->departmentName,
                $employee->baseSalary,
                $employee->salaryBonus,
                $employee->salaryBonusType,
                $employee->totalSalary
            ),
            $this->employeeFetcher->fetchAll()
        );
    }
}
