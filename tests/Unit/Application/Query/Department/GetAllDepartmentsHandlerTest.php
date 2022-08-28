<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Application\Query\Department;

use PayrollReport\Modules\Department\Application\Department\Department;
use PayrollReport\Modules\Department\Application\Department\DepartmentReadModel;
use PayrollReport\Modules\Department\Application\Department\Departments;
use PayrollReport\Modules\Department\Application\Department\GetAll\GetAllDepartmentsHandler;
use PayrollReport\Modules\Department\Application\Department\GetAll\GetAllDepartmentsQuery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetAllDepartmentsHandlerTest extends TestCase
{
    private DepartmentReadModel|MockObject $departmentReadModel;

    public function setUp(): void
    {
        $this->departmentReadModel = $this->createMock(DepartmentReadModel::class);
    }

    /** @test */
    public function shouldReturnAllDepartments(): void
    {
        $departments = new Departments(...[
            new Department(
                'test',
                'Test name',
                'percentage',
                500
            ),
            new Department(
                'test 2',
                'Test name 2',
                'fixed_amount',
                500
            ),
        ]);

        $this->departmentReadModel->expects(self::once())->method('fetchAll')->willReturn($departments);

        $result = call_user_func($this->getTestable(), new GetAllDepartmentsQuery());

        $this->assertEquals($result, $departments);
    }

    private function getTestable(): GetAllDepartmentsHandler
    {
        return new GetAllDepartmentsHandler(
            $this->departmentReadModel
        );
    }
}
