<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driven\Department;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PayrollReport\Modules\Department\Application\Department\Department;
use PayrollReport\Modules\Department\Application\Department\DepartmentReadModel;
use PayrollReport\Modules\Department\Application\Department\Departments;
use PayrollReport\Shared\Application\NotFoundException;

final class DepartmentDbReadModel implements DepartmentReadModel
{
    private readonly Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function fetchById(string $id): Department
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->select([
                'id',
                'name',
                'salary_bonus_type',
                'salary_bonus'
            ])
            ->from('departments')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        if ($result === false) {
            throw NotFoundException::notFoundById($id);
        }

        return new Department(
            $result['id'],
            $result['name'],
            $result['salary_bonus_type'],
            $result['salary_bonus'] / 100
        );
    }

    /**
     * @throws Exception
     */
    public function fetchAll(): Departments
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->select([
                'id',
                'name',
                'salary_bonus_type',
                'salary_bonus'
            ])
            ->from('departments')
            ->fetchAllAssociative();

        return new Departments(
            ...array_map(
                static fn (array $row): Department => new Department(
                    $row['id'],
                    $row['name'],
                    $row['salary_bonus_type'],
                    $row['salary_bonus'] / 100
                ),
                $result
            )
        );
    }
}
