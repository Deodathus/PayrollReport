<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Domain\Report;

interface ReportRepository
{
    public function fetch(string $id): Report;
}
