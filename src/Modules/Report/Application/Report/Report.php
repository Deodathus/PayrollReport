<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Report;

final class Report
{
    private array $reportPositions;

    public function __construct(ReportPosition ...$reportPosition)
    {
        $this->reportPositions = $reportPosition;
    }

    public function toArray(): array
    {
        return $this->reportPositions;
    }

    public function count(): int
    {
        return count($this->reportPositions);
    }
}
