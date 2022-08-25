<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Department\Store;

use PayrollReport\Shared\Application\Command\Command;

final class StoreDepartmentCommand implements Command
{
    public function __construct(
        public readonly string $name,
        public readonly int $salaryBonus,
        public readonly string $salaryBonusType
    ) {}
}
