<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Department;

use PayrollReport\Modules\Department\Domain\Employee\Employee;

final class DepartmentEmployees
{
    private array $employees;

    public function __construct(Employee ...$employee)
    {
        $this->employees = $employee;
    }

    public function toArray(): array
    {
        return $this->employees;
    }
}
