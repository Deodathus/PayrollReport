<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Report;

use PayrollReport\Shared\Application\Query\Filters;
use PayrollReport\Shared\Application\Query\Sort;

interface ReportPositionReadModel
{
    /**
     * @return ReportPosition[]
     */
    public function fetchByReportId(string $id, Filters $filters, ?Sort $sort = null): array;
}
