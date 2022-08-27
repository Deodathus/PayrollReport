<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driven\Department;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PayrollReport\Modules\Department\Domain\Department\BonusAmount;
use PayrollReport\Modules\Department\Domain\Department\Department;
use PayrollReport\Modules\Department\Domain\Department\DepartmentRepository;
use PayrollReport\Modules\Department\Domain\Department\DepartmentSalaryBonus;
use PayrollReport\Modules\Department\Domain\Department\DepartmentSalaryBonusType;
use PayrollReport\Modules\Department\Domain\InvalidArgumentException;
use PayrollReport\Shared\Application\NotFoundException;

final class DepartmentDbRepository implements DepartmentRepository
{
    private const DB_TABLE_NAME = 'departments';

    public function __construct(private readonly Connection $connection) {}

    /**
     * @throws Exception
     */
    public function existsWithId(string $id): bool
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
    public function store(Department $department): void
    {
        $snapshot = $department->getSnapshot();

        $this->connection
            ->createQueryBuilder()
            ->insert(self::DB_TABLE_NAME)
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

    /**
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws Exception
     */
    public function fetchSalaryBonus(string $id): DepartmentSalaryBonus
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->select(['salary_bonus', 'salary_bonus_type'])
            ->from(self::DB_TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        if ($result === false) {
            throw NotFoundException::notFoundById($id);
        }

        return new DepartmentSalaryBonus(
            DepartmentSalaryBonusType::from($result['salary_bonus_type']),
            new BonusAmount($result['salary_bonus'])
        );
    }

    /**
     * @throws Exception
     */
    public function fetchNames(): array
    {
        return $this->connection
            ->createQueryBuilder()
            ->select([
                'id',
                'name',
            ])
            ->from(self::DB_TABLE_NAME)
            ->fetchAllAssociativeIndexed();
    }
}
