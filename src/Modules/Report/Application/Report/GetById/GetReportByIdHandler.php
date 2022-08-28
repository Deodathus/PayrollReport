<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Report\GetById;

use PayrollReport\Modules\Report\Application\Report\Report;
use PayrollReport\Modules\Report\Application\Report\ReportPositionReadModel;
use PayrollReport\Modules\Report\Domain\ReportRepository;
use PayrollReport\Shared\Application\NotFoundException;
use PayrollReport\Shared\Application\Query\QueryHandler;

final class GetReportByIdHandler implements QueryHandler
{
    public function __construct(
        private readonly ReportRepository $reportRepository,
        private readonly ReportPositionReadModel $reportPositionReadModel
    ) {}

    /**
     * @throws NotFoundException
     */
    public function __invoke(GetReportByIdQuery $getReportQuery): Report
    {
        $reportExists = $this->reportRepository->existsById($getReportQuery->id);

        if (!$reportExists) {
            throw NotFoundException::notFoundById($getReportQuery->id);
        }

        return new Report(
            ...$this->reportPositionReadModel->fetchByReportId(
                $getReportQuery->id,
                $getReportQuery->filters,
                $getReportQuery->sort
            )
        );
    }
}
