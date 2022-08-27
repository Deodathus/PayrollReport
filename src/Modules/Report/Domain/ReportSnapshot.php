<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Domain;

use DateTimeImmutable;

final class ReportSnapshot
{
    /**
     * @param ReportPositionSnapshot[] $reportPositions
     */
    public function __construct(
        public readonly string $id,
        public readonly array $reportPositions,
        public readonly DateTimeImmutable $generatedAt
    ) {}
}
