<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Department\GetById;

use PayrollReport\Modules\Department\Application\Department\Department;
use PayrollReport\Modules\Department\Application\Department\DepartmentReadModel;
use PayrollReport\Shared\Application\Query\QueryHandler;

final class GetDepartmentByIdHandler implements QueryHandler
{
    public function __construct(private readonly DepartmentReadModel $departmentReadModel) {}

    public function __invoke(GetDepartmentByIdQuery $getDepartmentByIdCommand): Department
    {
        return $this->departmentReadModel->fetchById($getDepartmentByIdCommand->id);
    }
}
