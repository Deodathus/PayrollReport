<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Employee;

use DateTimeInterface;

final class EmployeeExperience
{
    public function __construct(public readonly DateTimeInterface $hiredAt) {}
}
