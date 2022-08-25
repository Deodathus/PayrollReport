<?php

namespace PayrollReport\Modules\Department\Domain\Department;

enum DepartmentSalaryBonusType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED_AMOUNT = 'fixed_amount';
}
