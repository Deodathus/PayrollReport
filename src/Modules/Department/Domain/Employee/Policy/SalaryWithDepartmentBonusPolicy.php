<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Employee\Policy;

use DateTimeImmutable;
use PayrollReport\Modules\Department\Domain\Department\DepartmentRepository;
use PayrollReport\Modules\Department\Domain\Department\DepartmentSalaryBonus;
use PayrollReport\Modules\Department\Domain\Employee\Employee;
use PayrollReport\Modules\Department\Domain\Employee\Salary;
use PayrollReport\Modules\Department\Domain\Employee\SalaryWithDepartmentBonus;
use PayrollReport\Modules\Department\Domain\Employee\Specification\FixedAmountSalaryBonusSpecification;
use PayrollReport\Modules\Department\Domain\Employee\Specification\PercentageSalaryBonusSpecification;
use PayrollReport\Modules\Department\Domain\InvalidArgumentException;
use RuntimeException;

final class SalaryWithDepartmentBonusPolicy
{
    public function __construct(
        private readonly PercentageSalaryBonusSpecification $percentageSalaryBonusSpecification,
        private readonly FixedAmountSalaryBonusSpecification $fixedAmountSalaryBonusSpecification,
        private readonly DepartmentRepository $departmentRepository
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function apply(Employee $employee): SalaryWithDepartmentBonus
    {
        $employeeSnapshot = $employee->getSnapshot();

        $departmentSalaryBonus = $this->departmentRepository->fetchSalaryBonus($employeeSnapshot->departmentId);

        if ($this->percentageSalaryBonusSpecification->isSatisfied($departmentSalaryBonus)) {
            return $this->applyForPercentageType(
                new Salary($employeeSnapshot->salary),
                $departmentSalaryBonus
            );
        }

        if ($this->fixedAmountSalaryBonusSpecification->isSatisfied($departmentSalaryBonus)) {
            return $this->applyForFixedAmountType(
                new Salary($employeeSnapshot->salary),
                $employeeSnapshot->hiredAt,
                $departmentSalaryBonus
            );
        }

        throw new RuntimeException('Department salary bonus does not satisfy salary policy!');
    }

    /**
     * @throws InvalidArgumentException
     */
    private function applyForPercentageType(
        Salary $baseSalary,
        DepartmentSalaryBonus $departmentSalaryBonus
    ): SalaryWithDepartmentBonus {
        $percentageBonus = $departmentSalaryBonus->bonusAmount->getNormalized() / 100;

        $salaryWithDepartmentBonus = (int) ($baseSalary->amount + ($baseSalary->amount * $percentageBonus));

        return new SalaryWithDepartmentBonus(
            new Salary($salaryWithDepartmentBonus),
            $departmentSalaryBonus->bonusType
        );
    }

    private function applyForFixedAmountType(
        Salary $baseSalary,
        DateTimeImmutable $employeeHiredAt,
        DepartmentSalaryBonus $departmentSalaryBonus
    ): SalaryWithDepartmentBonus {
        $employeeWorksYears = $employeeHiredAt->diff(new DateTimeImmutable())->y;
        if ($employeeWorksYears > 10) {
            $employeeWorksYears = 10;
        }

        $salaryWithDepartmentBonus = $departmentSalaryBonus->bonusAmount->amount * $employeeWorksYears + $baseSalary->amount;

        return new SalaryWithDepartmentBonus(
            new Salary($salaryWithDepartmentBonus),
            $departmentSalaryBonus->bonusType
        );
    }
}
