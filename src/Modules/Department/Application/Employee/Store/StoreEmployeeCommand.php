<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Employee\Store;

use DateTimeInterface;
use PayrollReport\Shared\Application\Command\Command;

final class StoreEmployeeCommand implements Command
{
    public function __construct(
        public readonly string $departmentId,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly DateTimeInterface $hiredAt,
        public readonly int $salary
    ) {}
}
