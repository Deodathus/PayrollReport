<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Domain;

interface ReportRepository
{
    public function store(Report $report): void;
}
