<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driven\Report;

use PayrollReport\Modules\Report\Domain\Report\Report;
use PayrollReport\Modules\Report\Domain\Report\ReportRepository;

final class ReportDbRepository implements ReportRepository
{
    public function fetch(string $id): Report
    {

    }
}
