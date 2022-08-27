<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Report\GetById;

use PayrollReport\Modules\Report\Application\Report\Report;
use PayrollReport\Modules\Report\Application\Report\ReportPositionReadModel;
use PayrollReport\Shared\Application\Query\QueryHandler;

final class GetReportByIdHandler implements QueryHandler
{
    public function __construct(
        private readonly ReportPositionReadModel $reportPositionReadModel
    ) {}

    public function __invoke(GetReportByIdQuery $getReportQuery): Report
    {
        return new Report(
            ...$this->reportPositionReadModel->fetchByReportId(
                $getReportQuery->id,
                $getReportQuery->filters,
                $getReportQuery->sort
            )
        );
    }
}
