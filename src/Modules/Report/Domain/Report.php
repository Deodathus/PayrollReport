<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Domain;

use DateTimeImmutable;

final class Report
{
    public function __construct(
        private readonly ReportId $id,
        private ReportPositions $reportPositions,
        private DateTimeImmutable $generatedAt
    ) {}

    public function getSnapshot(): ReportSnapshot
    {
        return new ReportSnapshot(
            $this->id->toString(),
            array_map(
                static fn (ReportPosition $reportPosition): ReportPositionSnapshot => $reportPosition->getSnapshot(),
                $this->reportPositions->toArray()
            ),
            $this->generatedAt
        );
    }
}
