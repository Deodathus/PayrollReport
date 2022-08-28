<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driven\Report;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PayrollReport\Modules\Report\Domain\Report;
use PayrollReport\Modules\Report\Domain\ReportPositionRepository;
use PayrollReport\Modules\Report\Domain\ReportRepository;

final class ReportDbRepository implements ReportRepository
{
    private const DB_TABLE_NAME = 'reports';

    public function __construct(
        private readonly Connection $connection,
        private readonly ReportPositionRepository $reportPositionRepository
    ) {}

    /**
     * @throws Exception
     */
    public function existsById(string $id): bool
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->select('count(id) as count')
            ->from(self::DB_TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchOne();

        return $result > 0;
    }

    /**
     * @throws Exception
     */
    public function store(Report $report): void
    {
        $reportSnapshot = $report->getSnapshot();

        $this->connection
            ->createQueryBuilder()
            ->insert(self::DB_TABLE_NAME)
            ->values([
                'id' => ':id',
                'generated_at' => ':generated_at',
            ])
            ->setParameters([
                'id' => $reportSnapshot->id,
                'generated_at' => $reportSnapshot->generatedAt->format('Y-m-d H:i:s'),
            ])
            ->executeStatement();

        foreach ($reportSnapshot->reportPositions as $reportPositionSnapshot) {
            $this->reportPositionRepository->store($reportPositionSnapshot);
        }
    }
}
