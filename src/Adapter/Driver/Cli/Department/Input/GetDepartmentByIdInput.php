<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driver\Cli\Department\Input;

use Assert\Assert;

final class GetDepartmentByIdInput
{
    private function __construct(public readonly string $id) {}

    public static function fromArray(array $data): self
    {
        $id = $data['departmentId'];

        Assert::lazy()
            ->that($id, 'id')->string()->notBlank()
            ->verifyNow();

        return new self(
            $id
        );
    }
}
