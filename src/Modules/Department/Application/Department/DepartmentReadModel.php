<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Department;

interface DepartmentReadModel
{
    public function fetchById(string $id): Department;

    public function fetchAll(): Departments;
}
