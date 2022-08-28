<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Employee;

final class EmployeeViewModel
{
    public function __construct(
        public readonly string $id,
        public readonly string $departmentId,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $hiredAt,
        public readonly float $salary
    ) {}
}
