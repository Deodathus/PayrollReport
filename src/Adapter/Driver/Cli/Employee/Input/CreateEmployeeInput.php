<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driver\Cli\Employee\Input;

use Assert\Assert;
use DateTimeImmutable;

final class CreateEmployeeInput
{
    private function __construct(
        public readonly string $departmentId,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly float $salary,
        public readonly DateTimeImmutable $hiredAt
    ) {}

    public static function fromArray(array $data): self
    {
        $departmentId = $data['departmentId'];
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $salary = $data['salary'];
        $hiredAt = $data['hiredAt'];

        $assertion = Assert::lazy()
            ->that($departmentId, 'departmentId')->string()->notBlank()
            ->that($firstName, 'firstName')->string()->notBlank()
            ->that($lastName, 'lastName')->string()->notBlank()
            ->that($salary, 'salary')->numeric()->greaterThan(0);

        if ($hiredAt !== 'now') {
            $assertion->that($hiredAt, 'hiredAt')->date('Ymd');
        }

        $assertion->verifyNow();

        return new self(
            $departmentId,
            $firstName,
            $lastName,
            round((float) $salary, 2),
            new DateTimeImmutable($hiredAt)
        );
    }
}
