<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Employee;

interface EmployeeRepository
{
    public function store(Employee $employee): void;

    /**
     * @return Employee[]
     */
    public function fetchAll(): array;
}
