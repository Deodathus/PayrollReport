<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Employee;

final class EmployeeName
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName
    ) {}
}
