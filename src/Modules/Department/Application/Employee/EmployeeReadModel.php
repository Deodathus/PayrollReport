<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Employee;

interface EmployeeReadModel
{
    public function fetchAll(): Employees;
}
