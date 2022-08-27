<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Report;

final class ReportData
{
    public function __construct(
        public readonly string $id,
        public readonly string $generatedAt
    ) {}
}
