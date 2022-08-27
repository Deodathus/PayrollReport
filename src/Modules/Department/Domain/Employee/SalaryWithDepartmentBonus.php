<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Employee;

use PayrollReport\Modules\Department\Domain\Department\DepartmentSalaryBonusType;

final class SalaryWithDepartmentBonus
{
    public function __construct(
        public readonly Salary $salary,
        public readonly DepartmentSalaryBonusType $departmentSalaryBonusType
    ) {}
}
