<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driven\Employee;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PayrollReport\Modules\Department\Application\Employee\Employee;
use PayrollReport\Modules\Department\Application\Employee\EmployeeReadModel;
use PayrollReport\Modules\Department\Application\Employee\Employees;

final class EmployeeDbReadModel implements EmployeeReadModel
{
    public function __construct(private readonly Connection $connection) {}

    /**
     * @throws Exception
     */
    public function fetchAll(): Employees
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
            ->from('employees')
            ->fetchAllAssociative();

        return new Employees(
            ...array_map(
                static fn (array $row): Employee => new Employee(
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
