<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Report;

interface ReportReadModel
{
    public function fetchReportsData(): ReportsData;
}
