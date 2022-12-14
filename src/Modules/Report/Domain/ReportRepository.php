<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Domain;

interface ReportRepository
{
    public function existsById(string $id): bool;

    public function store(Report $report): void;
}
