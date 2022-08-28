<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driven\Employee;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PayrollReport\Modules\Department\Application\Employee\EmployeeViewModel;
use PayrollReport\Modules\Department\Application\Employee\EmployeeReadModel;
use PayrollReport\Modules\Department\Application\Employee\EmployeesViewModels;

final class EmployeeDbReadModel implements EmployeeReadModel
{
    private const DB_TABLE_NAME = 'employees';

    public function __construct(private readonly Connection $connection) {}

    /**
     * @throws Exception
     */
    public function fetchAll(): EmployeesViewModels
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->select([
                'id',
                'department_id',
                'first_name',
                'last_name',
                'hired_at',
                'salary'
            ])
            ->from(self::DB_TABLE_NAME)
            ->fetchAllAssociative();

        return new EmployeesViewModels(
            ...array_map(
                static fn (array $row): EmployeeViewModel => new EmployeeViewModel(
                    $row['id'],
                    $row['department_id'],
                    $row['first_name'],
                    $row['last_name'],
                    $row['hired_at'],
                    $row['salary'] / 100
                ),
            $result)
        );
    }
}
