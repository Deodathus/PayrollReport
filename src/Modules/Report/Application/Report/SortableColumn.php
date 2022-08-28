<?php

namespace PayrollReport\Modules\Report\Application\Report;

enum SortableColumn: string
{
    case EMPLOYEE_FIRST_NAME = 'employee_first_name';
    case EMPLOYEE_LAST_NAME = 'employee_last_name';
    case DEPARTMENT_NAME = 'department_name';
    case BASE_SALARY = 'base_salary';
    case SALARY_BONUS = 'salary_bonus';
    case SALARY_BONUS_TYPE = 'salary_bonus_type';
    case TOTAL_SALARY = 'total_salary';
}
