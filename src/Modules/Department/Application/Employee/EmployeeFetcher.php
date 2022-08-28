<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Employee;

use PayrollReport\Modules\Department\Application\Department\DepartmentReadModel;
use PayrollReport\Modules\Department\Domain\Employee\Employee as EmployeeEntity;
use PayrollReport\Modules\Department\Domain\Employee\EmployeeRepository;
use PayrollReport\Modules\Department\Domain\Employee\Policy\SalaryWithDepartmentBonusPolicy;

final class EmployeeFetcher
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
        private readonly DepartmentReadModel $departmentReadModel,
        private readonly SalaryWithDepartmentBonusPolicy $salaryWithDepartmentBonusPolicy
    ) {}

    /**
     * @return Employee[]
     */
    public function fetchAll(): array
    {
        $departmentNames = $this->departmentReadModel->fetchNames();
        $employees = $this->employeeRepository->fetchAll();

        return array_map(
            function (EmployeeEntity $employee) use ($departmentNames): Employee {
                $employeeSnapshot = $employee->getSnapshot();

                $totalSalary = $employee->getSalaryWithDepartmentBonus($this->salaryWithDepartmentBonusPolicy);

                $salary = $employeeSnapshot->salary;
                $salaryBonus = $totalSalary->salary->amount - $salary;

                return new Employee(
                    $employeeSnapshot->id,
                    $employeeSnapshot->departmentId,
                    $departmentNames[$employeeSnapshot->departmentId]['name'],
                    $employeeSnapshot->firstName,
                    $employeeSnapshot->lastName,
                    $salary,
                    $salaryBonus,
                    $totalSalary->departmentSalaryBonusType->value,
                    $totalSalary->salary->amount
                );
            },
            $employees
        );
    }
}
