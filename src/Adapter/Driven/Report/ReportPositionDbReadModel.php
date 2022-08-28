<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driven\Report;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\QueryBuilder;
use PayrollReport\Modules\Report\Application\Report\FilterableColumn;
use PayrollReport\Modules\Report\Application\Report\ReportPosition;
use PayrollReport\Modules\Report\Application\Report\ReportPositionReadModel;
use PayrollReport\Modules\Report\Application\Report\SortableColumn;
use PayrollReport\Shared\Application\Query\Filter;
use PayrollReport\Shared\Application\Query\Filters;
use PayrollReport\Shared\Application\Query\Sort;

final class ReportPositionDbReadModel implements ReportPositionReadModel
{
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

        $filtersExpressions = [];
        foreach ($filters->toArray() as $filter) {
            $filtersExpressions[] = $this->applyFilter($queryBuilder, $filter);
        }

        $queryBuilder->where(
            $queryBuilder->expr()->and(
                $queryBuilder->expr()->eq('report_id', ':reportId'),
                count($filtersExpressions) ? $queryBuilder->expr()->or(
                    ...$filtersExpressions
                ) : null
            )
        )->setParameter('reportId', $id);

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
        if ($sort && $this->isColumnSortable($sort->column)) {
            $queryBuilder->addOrderBy($sort->column, $sort->order->value);
        }
    }

    private function isColumnSortable(string $sortableColumn): bool
    {
        return in_array(
            $sortableColumn,
            array_map(static fn(SortableColumn $column): string => $column->value, SortableColumn::cases()),
            true
        );
    }

    private function applyFilter(QueryBuilder $queryBuilder, Filter $filter): CompositeExpression
    {
        if ($this->isColumnNotSupported($filter->column)) {
            throw new \RuntimeException(sprintf('Filter [%s] is not supported!', $filter->column));
        }

        $queryBuilder->setParameter(sprintf('%s', $filter->value), "%{$filter->value}%");

        return $queryBuilder->expr()->or(sprintf('%s like :%s', $filter->column, $filter->value));
    }

    private function isColumnNotSupported(string $column): bool
    {
        return !in_array(
            $column,
            array_map(static fn(FilterableColumn $column): string => $column->value, FilterableColumn::cases()),
            true
        );
    }
}
