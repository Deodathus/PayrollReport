<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Domain\Employee;

use DateTimeImmutable;
use PayrollReport\Modules\Department\Domain\Department\DepartmentId;
use PayrollReport\Modules\Department\Domain\Employee\Employee;
use PayrollReport\Modules\Department\Domain\Employee\EmployeeExperience;
use PayrollReport\Modules\Department\Domain\Employee\EmployeeName;
use PayrollReport\Modules\Department\Domain\Employee\Salary;

final class EmployeeFactory
{
    public static function create(DateTimeImmutable $hiredAt, int $salaryAmount): Employee
    {
        return Employee::create(
            DepartmentId::generate(),
            new EmployeeName('Test first name', 'Test last name'),
            new EmployeeExperience($hiredAt),
            new Salary($salaryAmount)
        );
    }
}
