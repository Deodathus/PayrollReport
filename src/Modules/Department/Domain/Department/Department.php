<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Department;

use PayrollReport\Modules\Department\Domain\Employee\Employee;
use PayrollReport\Modules\Department\Domain\Employee\EmployeeSnapshot;

final class Department
{
    public function __construct(
        private readonly DepartmentId $id,
        private string $name,
        private DepartmentEmployees $departmentEmployees,
        private DepartmentSalaryBonus $salaryBonus
    ) {}

    public static function create(
        string $name,
        DepartmentSalaryBonus $salaryBonus
    ): self {
        return new self(
            DepartmentId::generate(),
            $name,
            new DepartmentEmployees(),
            $salaryBonus
        );
    }

    public function getSnapshot(): DepartmentSnapshot
    {
        return new DepartmentSnapshot(
            $this->id->toString(),
            $this->name,
            array_map(
                static fn (Employee $employee): EmployeeSnapshot => $employee->getSnapshot(),
                $this->departmentEmployees->toArray()
            ),
            $this->salaryBonus->bonusType,
            $this->salaryBonus->bonusAmount->amount
        );
    }
}
