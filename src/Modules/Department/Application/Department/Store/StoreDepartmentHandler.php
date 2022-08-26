<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Department\Store;

use PayrollReport\Modules\Department\Domain\Department\Department;
use PayrollReport\Modules\Department\Domain\Department\DepartmentRepository;
use PayrollReport\Modules\Department\Domain\Department\DepartmentSalaryBonus;
use PayrollReport\Modules\Department\Domain\Department\DepartmentSalaryBonusType;
use PayrollReport\Modules\Department\Domain\Salary;
use PayrollReport\Shared\Application\Command\CommandHandler;

final class StoreDepartmentHandler implements CommandHandler
{
    public function __construct(private readonly DepartmentRepository $departmentRepository) {}

    public function __invoke(StoreDepartmentCommand $storeDepartmentCommand): void
    {
        $department = Department::create(
            $storeDepartmentCommand->name,
            new DepartmentSalaryBonus(
                DepartmentSalaryBonusType::from($storeDepartmentCommand->salaryBonusType),
                new Salary((int) ($storeDepartmentCommand->salaryBonus * 100))
            )
        );

        $this->departmentRepository->store($department);
    }
}
