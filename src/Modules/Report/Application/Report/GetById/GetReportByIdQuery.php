<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Report\GetById;

use PayrollReport\Shared\Application\Query\Filters;
use PayrollReport\Shared\Application\Query\Query;
use PayrollReport\Shared\Application\Query\Sort;

final class GetReportByIdQuery implements Query
{
    public function __construct(
        public readonly string $id,
        public readonly Filters $filters,
        public readonly ?Sort $sort = null
    ) {}
}
