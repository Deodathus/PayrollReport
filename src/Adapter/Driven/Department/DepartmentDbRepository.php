<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driven\Department;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PayrollReport\Modules\Department\Domain\Department\Department;
use PayrollReport\Modules\Department\Domain\Department\DepartmentRepository;

final class DepartmentDbRepository implements DepartmentRepository
{
    public function __construct(private readonly Connection $connection) {}

    /**
     * @throws Exception
     */
    public function existsWithId(string $id): bool
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->select('count(id) as count')
            ->from('departments')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchOne();

        return $result > 0;
    }

    /**
     * @throws Exception
     */
    public function store(Department $department): void
    {
        $snapshot = $department->getSnapshot();

        $this->connection
            ->createQueryBuilder()
            ->insert('departments')
            ->values([
                'id' => ':id',
                'name' => ':name',
                'salary_bonus' => ':salaryBonus',
                'salary_bonus_type' => ':salaryBonusType',
            ])
            ->setParameters([
                'id' => $snapshot->id,
                'name' => $snapshot->name,
                'salaryBonus' => $snapshot->departmentSalaryBonusAmount,
                'salaryBonusType' => $snapshot->departmentSalaryBonusType->value,
            ])
            ->executeStatement();
    }
}
