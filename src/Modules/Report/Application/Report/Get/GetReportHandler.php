<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Report\Get;

use PayrollReport\Shared\Application\Query\QueryHandler;

final class GetReportHandler implements QueryHandler
{
    public function __construct() {}

    public function __invoke(GetReportQuery $getReportQuery)
    {
    }
}
