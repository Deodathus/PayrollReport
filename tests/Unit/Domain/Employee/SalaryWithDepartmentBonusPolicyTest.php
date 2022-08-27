<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Domain\Employee;

use DateTimeImmutable;
use PayrollReport\Modules\Department\Domain\Department\BonusAmount;
use PayrollReport\Modules\Department\Domain\Department\DepartmentRepository;
use PayrollReport\Modules\Department\Domain\Department\DepartmentSalaryBonus;
use PayrollReport\Modules\Department\Domain\Department\DepartmentSalaryBonusType;
use PayrollReport\Modules\Department\Domain\Employee\Policy\SalaryWithDepartmentBonusPolicy;
use PayrollReport\Modules\Department\Domain\Employee\Specification\FixedAmountSalaryBonusSpecification;
use PayrollReport\Modules\Department\Domain\Employee\Specification\PercentageSalaryBonusSpecification;
use PayrollReport\Tests\Unit\Domain\EmployeeFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SalaryWithDepartmentBonusPolicyTest extends TestCase
{
    private const EMPLOYEE_SALARY = 1000000;

    private const DEPARTMENT_BONUS_IN_PERCENT = 5000;

    private const DEPARTMENT_BONUS_IN_FIXED_AMOUNT = 100000;

    private DepartmentRepository|MockObject $departmentRepository;

    public function setUp(): void
    {
        $this->departmentRepository = $this->createMock(DepartmentRepository::class);
    }

    /**
     * @test
     */
    public function shouldApplyPercentageType(): void
    {
        $fiftyPercent = self::DEPARTMENT_BONUS_IN_PERCENT;

        $this->departmentRepository
            ->expects(self::once())
            ->method('fetchSalaryBonus')
            ->willReturn(
                new DepartmentSalaryBonus(
                    DepartmentSalaryBonusType::PERCENTAGE,
                    new BonusAmount($fiftyPercent)
                )
            );

        $employee = EmployeeFactory::create(new DateTimeImmutable(), self::EMPLOYEE_SALARY);

        $salaryWithDepartmentBonus = $this->getTestable()->apply($employee);

        $percentageValue = self::EMPLOYEE_SALARY * (($fiftyPercent / 100) / 100);

        $this->assertSame(
            $salaryWithDepartmentBonus->salary->amount,
            (int) $percentageValue + self::EMPLOYEE_SALARY
        );
    }

    /**
     * @test
     * @dataProvider fixedAmountTypeDataProvider
     */
    public function shouldApplyFixedAmountType(int $yearsWorks): void
    {
        $this->departmentRepository
            ->expects(self::once())
            ->method('fetchSalaryBonus')
            ->willReturn(
                new DepartmentSalaryBonus(
                    DepartmentSalaryBonusType::FIXED_AMOUNT,
                    new BonusAmount(self::DEPARTMENT_BONUS_IN_FIXED_AMOUNT)
                )
            );

        $employee = EmployeeFactory::create(
            new DateTimeImmutable(date('Y-m-d', strtotime(sprintf('-%d year', $yearsWorks), time()))),
            self::EMPLOYEE_SALARY
        );

        $employeeWorksYears = $employee->getSnapshot()->hiredAt->diff(new DateTimeImmutable())->y;
        if ($employeeWorksYears > 10) {
            $employeeWorksYears = 10;
        }

        $salaryWithDepartmentBonus = $this->getTestable()->apply($employee);

        $this->assertSame(
            $salaryWithDepartmentBonus->salary->amount,
            $employeeWorksYears * self::DEPARTMENT_BONUS_IN_FIXED_AMOUNT + self::EMPLOYEE_SALARY
        );
    }

    public function fixedAmountTypeDataProvider(): array
    {
        return [
            [2],
            [5],
            [10],
            [14]
        ];
    }

    private function getTestable(): SalaryWithDepartmentBonusPolicy
    {
        return new SalaryWithDepartmentBonusPolicy(
            new PercentageSalaryBonusSpecification(),
            new FixedAmountSalaryBonusSpecification(),
            $this->departmentRepository
        );
    }
}
