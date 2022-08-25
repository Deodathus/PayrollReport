<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Department;

use PayrollReport\Modules\Department\Domain\Salary;

final class DepartmentSalaryBonus
{
    public function __construct(
        public readonly DepartmentSalaryBonusType $bonusType,
        public readonly Salary $bonusAmount
    ) {}
}
