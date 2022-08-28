<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Department\GetAll;

use PayrollReport\Modules\Department\Application\Department\DepartmentReadModel;
use PayrollReport\Modules\Department\Application\Department\Departments;
use PayrollReport\Shared\Application\Query\QueryHandler;

final class GetAllDepartmentsHandler implements QueryHandler
{
    public function __construct(
        private readonly DepartmentReadModel $departmentReadModel
    ) {}

    public function __invoke(GetAllDepartmentsQuery $allDepartmentsQuery): Departments
    {
        return $this->departmentReadModel->fetchAll();
    }
}
