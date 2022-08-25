<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driven\Employee;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PayrollReport\Modules\Department\Domain\Employee\Employee;
use PayrollReport\Modules\Department\Domain\Employee\EmployeeRepository;

final class EmployeeDbRepository implements EmployeeRepository
{
    public function __construct(private readonly Connection $connection) {}

    /**
     * @throws Exception
     */
    public function store(Employee $employee): void
    {
        $snapshot = $employee->getSnapshot();

        $this->connection
            ->createQueryBuilder()
            ->insert('employees')
            ->values([
                'id' => ':id',
                'department_id' => ':departmentId',
                'first_name' => ':firstName',
                'last_name' => ':lastName',
                'hired_at' => ':hiredAt',
                'salary' => ':salary',
            ])
            ->setParameters([
                'id' => $snapshot->id,
                'departmentId' => $snapshot->departmentId,
                'firstName' => $snapshot->firstName,
                'lastName' => $snapshot->lastName,
                'hiredAt' => $snapshot->hiredAt->format('Y-m-d H:i:s'),
                'salary' => $snapshot->salary
            ])
            ->executeStatement();
    }
}
