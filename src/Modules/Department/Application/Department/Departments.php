<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Department;

final class Departments
{
    private readonly array $departments;

    public function __construct(Department ...$department)
    {
        $this->departments = $department;
    }

    public function toArray(): array
    {
        return $this->departments;
    }
}
