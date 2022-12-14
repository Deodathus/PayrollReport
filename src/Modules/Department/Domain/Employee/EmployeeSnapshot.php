<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Employee;

use DateTimeImmutable;

final class EmployeeSnapshot
{
    public function __construct(
        public readonly string $id,
        public readonly string $departmentId,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly DateTimeImmutable $hiredAt,
        public readonly int $salary
    ) {}
}
