<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driven\Employee;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PayrollReport\Modules\Department\Domain\Department\DepartmentId;
use PayrollReport\Modules\Department\Domain\Employee\Employee;
use PayrollReport\Modules\Department\Domain\Employee\EmployeeExperience;
use PayrollReport\Modules\Department\Domain\Employee\EmployeeId;
use PayrollReport\Modules\Department\Domain\Employee\EmployeeName;
use PayrollReport\Modules\Department\Domain\Employee\EmployeeRepository;
use PayrollReport\Modules\Department\Domain\Employee\Salary;
use PayrollReport\Modules\Department\Domain\InvalidArgumentException;

final class EmployeeDbRepository implements EmployeeRepository
{
    private const DB_TABLE_NAME = 'employees';

    public function __construct(private readonly Connection $connection) {}

    /**
     * @throws Exception
     */
    public function store(Employee $employee): void
    {
        $snapshot = $employee->getSnapshot();

        $this->connection
            ->createQueryBuilder()
            ->insert(self::DB_TABLE_NAME)
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

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function fetchAll(): array
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

        return array_map(
            static fn(array $row): Employee => new Employee(
                EmployeeId::fromString($row['id']),
                DepartmentId::fromString($row['department_id']),
                new EmployeeName($row['first_name'], $row['last_name']),
                new EmployeeExperience(new DateTimeImmutable($row['hired_at'])),
                new Salary($row['salary'])
            ),
            $result
        );
    }
}
