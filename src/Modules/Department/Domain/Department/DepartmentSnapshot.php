<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Department;

use PayrollReport\Modules\Department\Domain\Employee\EmployeeSnapshot;

final class DepartmentSnapshot
{
    /**
     * @param EmployeeSnapshot[] $employees
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly array $employees,
        public readonly DepartmentSalaryBonusType $departmentSalaryBonusType,
        public readonly int $departmentSalaryBonusAmount
    ) {}
}
