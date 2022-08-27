<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Employee\Specification;

use PayrollReport\Modules\Department\Domain\Department\DepartmentSalaryBonus;
use PayrollReport\Modules\Department\Domain\Department\DepartmentSalaryBonusType;

final class FixedAmountSalaryBonusSpecification
{
    public function isSatisfied(DepartmentSalaryBonus $departmentSalaryBonus): bool
    {
        return $departmentSalaryBonus->bonusType === DepartmentSalaryBonusType::FIXED_AMOUNT;
    }
}
