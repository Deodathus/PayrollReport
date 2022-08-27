<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driven\Report;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PayrollReport\Modules\Report\Application\Report\ReportData;
use PayrollReport\Modules\Report\Application\Report\ReportReadModel;
use PayrollReport\Modules\Report\Application\Report\ReportsData;

final class ReportDbReadModel implements ReportReadModel
{
    private const DB_TABLE_NAME = 'reports';

    public function __construct(private readonly Connection $connection) {}

    /**
     * @throws Exception
     */
    public function fetchReportsData(): ReportsData
    {
        $results = $this->connection
            ->createQueryBuilder()
            ->select([
                'id',
                'generated_at',
            ])
            ->from(self::DB_TABLE_NAME)
            ->fetchAllAssociative();

        return new ReportsData(
            ...array_map(
                static fn (array $row): ReportData => new ReportData(
                    $row['id'],
                    $row['generated_at']
                ),
                $results
            )
        );
    }
}
