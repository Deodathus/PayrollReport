<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Employee;

final class EmployeesViewModels
{
    private readonly array $employees;

    public function __construct(EmployeeViewModel ...$employee)
    {
        $this->employees = $employee;
    }

    public function toArray(): array
    {
        return $this->employees;
    }

    public function count(): int
    {
        return count($this->employees);
    }
}
