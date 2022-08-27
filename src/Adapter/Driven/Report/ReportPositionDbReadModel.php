<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driven\Report;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use PayrollReport\Modules\Report\Application\Report\ReportPosition;
use PayrollReport\Modules\Report\Application\Report\ReportPositionReadModel;
use PayrollReport\Shared\Application\Query\Filters;
use PayrollReport\Shared\Application\Query\Sort;

final class ReportPositionDbReadModel implements ReportPositionReadModel
{
    private const SUPPORTED_FILTERS = [
        'employee_first_name',
        'employee_last_name',
        'department_name'
    ];

    private const SUPPORTED_SORT_COLUMNS = [
        'employee_first_name',
        'employee_last_name',
        'department_name',
        'base_salary',
        'salary_bonus',
        'salary_bonus_type',
        'total_salary',
    ];

    private const SUPPORTED_SORT_ORDERS = [
        'ASC',
        'DESC',
    ];

    private const DB_TABLE_NAME = 'reports_positions';

    public function __construct(private readonly Connection $connection) {}

    /**
     * @return ReportPosition[]
     * @throws Exception
     */
    public function fetchByReportId(string $id, Filters $filters, ?Sort $sort = null): array
    {
        $queryBuilder = $this->connection
            ->createQueryBuilder()
            ->select([
                'employee_first_name',
                'employee_last_name',
                'department_name',
                'base_salary',
                'salary_bonus',
                'salary_bonus_type',
                'total_salary',
            ])
            ->from(self::DB_TABLE_NAME);

        $this->applySort($queryBuilder, $sort);
        $this->applyFilters($queryBuilder, $filters);

        $result = $queryBuilder->fetchAllAssociative();

        return array_map(
            static fn (array $row): ReportPosition => new ReportPosition(
                $row['employee_first_name'],
                $row['employee_last_name'],
                $row['department_name'],
                $row['base_salary'] / 100,
                $row['salary_bonus'] / 100,
                $row['salary_bonus_type'],
                $row['total_salary'] / 100
            ),
            $result
        );
    }

    private function applySort(QueryBuilder $queryBuilder, ?Sort $sort = null): void
    {
        if (
            $sort &&
            in_array($sort->column, self::SUPPORTED_SORT_COLUMNS) &&
            in_array($sort->order->value, self::SUPPORTED_SORT_ORDERS)
        ) {
            $queryBuilder->addOrderBy($sort->column, $sort->order->value);
        }
    }

    private function applyFilters(QueryBuilder $queryBuilder, Filters $filters): void
    {
        foreach ($filters->toArray() as $filter) {
            if (!in_array($filter->column, self::SUPPORTED_FILTERS)) {
                continue;
            }

            $queryBuilder->orWhere(sprintf('%s like :%s', $filter->column, $filter->value))
                ->setParameter(sprintf('%s', $filter->value), "%{$filter->value}%");
        }
    }
}
