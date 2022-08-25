<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Report\Get;

use PayrollReport\Modules\Report\Domain\Report\ReportRepository;
use PayrollReport\Shared\Application\Query\QueryHandler;

final class GetReportHandler implements QueryHandler
{
    public function __construct(private readonly ReportRepository $reportRepository) {}

    public function __invoke(GetReportQuery $getReportQuery)
    {
        $report = $this->reportRepository->fetch();
    }
}
