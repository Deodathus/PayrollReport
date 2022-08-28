<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Application\Query\Employee;

use PayrollReport\Modules\Department\Application\Employee\EmployeeReadModel;
use PayrollReport\Modules\Department\Application\Employee\EmployeesViewModels;
use PayrollReport\Modules\Department\Application\Employee\EmployeeViewModel;
use PayrollReport\Modules\Department\Application\Employee\GetAll\GetAllEmployeesHandler;
use PayrollReport\Modules\Department\Application\Employee\GetAll\GetAllEmployeesQuery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetAllEmployeesHandlerTest extends TestCase
{
    private EmployeeReadModel|MockObject $employeeReadModel;

    public function setUp(): void
    {
        $this->employeeReadModel = $this->createMock(EmployeeReadModel::class);
    }

    /** @test */
    public function shouldReturnAllEmployees(): void
    {
        $employeesViewModels = new EmployeesViewModels(...[
            new EmployeeViewModel(
                'test',
                'test department',
                'test first name',
                'test last name',
                'today',
                500
            ),
            new EmployeeViewModel(
                'test 2',
                'test department 2',
                'test first name 2',
                'test last name 2',
                'today',
                1500
            ),
        ]);

        $this->employeeReadModel->expects(self::once())->method('fetchAll')->willReturn($employeesViewModels);

        $result = call_user_func($this->getTestable(), new GetAllEmployeesQuery());

        $this->assertEquals($employeesViewModels, $result);
    }

    private function getTestable(): GetAllEmployeesHandler
    {
        return new GetAllEmployeesHandler(
            $this->employeeReadModel
        );
    }
}
