<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Employee;

final class Employees
{
    private readonly array $employees;

    public function __construct(Employee ...$employee)
    {
        $this->employees = $employee;
    }

    public function toArray(): array
    {
        return $this->employees;
    }
}
