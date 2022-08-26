<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Department\GetById;

use PayrollReport\Shared\Application\Query\Query;

final class GetDepartmentByIdQuery implements Query
{
    public function __construct(public readonly string $id) {}
}
