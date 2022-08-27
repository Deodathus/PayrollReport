<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Domain;

final class ReportPositions
{
    private readonly array $reportPositions;

    public function __construct(ReportPosition ...$reportPositions)
    {
        $this->reportPositions = $reportPositions;
    }

    public function toArray(): array
    {
        return $this->reportPositions;
    }
}
