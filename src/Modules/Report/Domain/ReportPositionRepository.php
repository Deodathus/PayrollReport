<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Domain;

interface ReportPositionRepository
{
    public function store(ReportPositionSnapshot $reportPositions): void;
}
