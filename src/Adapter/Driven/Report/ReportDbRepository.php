<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driven\Report;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PayrollReport\Modules\Report\Domain\Report;
use PayrollReport\Modules\Report\Domain\ReportPositionRepository;
use PayrollReport\Modules\Report\Domain\ReportRepository;
use Throwable;

final class ReportDbRepository implements ReportRepository
{
    private const DB_TABLE_NAME = 'reports';

    public function __construct(
        private readonly Connection $connection,
        private readonly ReportPositionRepository $reportPositionRepository
    ) {}

    /**
     * @throws Throwable
     * @throws Exception
     */
    public function store(Report $report): void
    {
        $reportSnapshot = $report->getSnapshot();

        try {
            $this->connection->beginTransaction();

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

            $this->connection->commit();
        } catch (Throwable $exception) {
            $this->connection->rollBack();

            throw $exception;
        }
    }
}
