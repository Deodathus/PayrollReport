<?php

namespace PayrollReport\Modules\Report\Application\Report;

enum FilterableColumn: string
{
    case EMPLOYEE_FIRST_NAME = 'employee_first_name';
    case EMPLOYEE_LAST_NAME = 'employee_last_name';
    case DEPARTMENT_NAME = 'department_name';
}
