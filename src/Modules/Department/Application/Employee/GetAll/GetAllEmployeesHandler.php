<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Employee\GetAll;

use PayrollReport\Modules\Department\Application\Employee\EmployeeReadModel;
use PayrollReport\Modules\Department\Application\Employee\EmployeesViewModels;
use PayrollReport\Shared\Application\Query\QueryHandler;

final class GetAllEmployeesHandler implements QueryHandler
{
    public function __construct(private readonly EmployeeReadModel $employeeReadModel) {}

    public function __invoke(GetAllEmployeesQuery $allEmployeesCommand): EmployeesViewModels
    {
        return $this->employeeReadModel->fetchAll();
    }
}
