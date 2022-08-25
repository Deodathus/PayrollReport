<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Employee;

use PayrollReport\Modules\Department\Domain\Department\DepartmentId;
use PayrollReport\Modules\Department\Domain\Salary;

final class Employee
{
    public function __construct(
        private readonly EmployeeId $id,
        private DepartmentId $departmentId,
        private EmployeeName $employeeName,
        private EmployeeExperience $employeeExperience,
        private Salary $salary
    ) {}

    public static function create(
        DepartmentId $departmentId,
        EmployeeName $employeeName,
        EmployeeExperience $employeeExperience,
        Salary $salary
    ): self {
        return new self(
            EmployeeId::generate(),
            $departmentId,
            $employeeName,
            $employeeExperience,
            $salary
        );
    }

    public function getSnapshot(): EmployeeSnapshot
    {
        return new EmployeeSnapshot(
            $this->id->toString(),
            $this->departmentId->toString(),
            $this->employeeName->firstName,
            $this->employeeName->lastName,
            $this->employeeExperience->hiredAt,
            $this->salary->amount
        );
    }
}
