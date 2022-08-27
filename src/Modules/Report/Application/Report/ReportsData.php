<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Report;

final class ReportsData
{
    private array $reportsData;

    public function __construct(ReportData ...$reportsData)
    {
        $this->reportsData = $reportsData;
    }

    public function toArray(): array
    {
        return $this->reportsData;
    }

    public function count(): int
    {
        return count($this->reportsData);
    }
}
