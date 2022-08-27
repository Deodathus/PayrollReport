<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Report\GetAllData;

use PayrollReport\Modules\Report\Application\Report\ReportReadModel;
use PayrollReport\Modules\Report\Application\Report\ReportsData;
use PayrollReport\Shared\Application\Query\QueryHandler;

final class GetReportsDataHandler implements QueryHandler
{
    public function __construct(private readonly ReportReadModel $reportReadModel) {}

    public function __invoke(GetReportsDataQuery $getReportsDataQuery): ReportsData
    {
        return $this->reportReadModel->fetchReportsData();
    }
}
