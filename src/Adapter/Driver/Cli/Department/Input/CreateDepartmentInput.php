<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driver\Cli\Department\Input;

use Assert\Assert;
use PayrollReport\Modules\Department\Domain\Department\DepartmentSalaryBonusType;

final class CreateDepartmentInput
{
    private function __construct(
        public readonly string $name,
        public readonly float $salaryBonus,
        public readonly string $salaryBonusType
    ) {}

    public static function fromArray(array $data): self
    {
        $name = $data['name'];
        $salaryBonus = $data['salaryBonus'];
        $salaryBonusType = $data['salaryBonusType'];

        Assert::lazy()
            ->that($name, 'name')->string()->notBlank()
            ->that($salaryBonus, 'salaryBonus')->numeric()->greaterThan(0)
            ->that($salaryBonusType, 'salaryBonusType')->inArray(
                array_map(
                    static fn (DepartmentSalaryBonusType $bonusType) => $bonusType->value,
                    DepartmentSalaryBonusType::cases()
                )
            )
            ->verifyNow();

        return new self(
            $name,
            round((float) $salaryBonus, 2),
            $salaryBonusType
        );
    }
}
