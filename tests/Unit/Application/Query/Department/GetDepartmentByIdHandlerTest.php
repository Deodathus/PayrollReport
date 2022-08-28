<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Application\Query\Department;

use PayrollReport\Modules\Department\Application\Department\Department;
use PayrollReport\Modules\Department\Application\Department\DepartmentReadModel;
use PayrollReport\Modules\Department\Application\Department\GetById\GetDepartmentByIdHandler;
use PayrollReport\Modules\Department\Application\Department\GetById\GetDepartmentByIdQuery;
use PayrollReport\Shared\Application\NotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class GetDepartmentByIdHandlerTest extends TestCase
{
    private DepartmentReadModel|MockObject $departmentReadModel;

    public function setUp(): void
    {
        $this->departmentReadModel = $this->createMock(DepartmentReadModel::class);
    }

    /** @test */
    public function shouldReturnDepartment(): void
    {
        $departmentReadModel = new Department(
            'test',
            'Test name',
            'fixed_amount',
            500
        );
        $this->departmentReadModel->expects(self::once())->method('fetchById')->willReturn($departmentReadModel);

        $result = call_user_func($this->getTestable(), new GetDepartmentByIdQuery('test'));

        $this->assertEquals($departmentReadModel, $result);
    }

    /** @test */
    public function shouldThrowNotFoundException(): void
    {
        $this->departmentReadModel
            ->expects(self::once())
            ->method('fetchById')
            ->willThrowException(NotFoundException::notFoundById('test'));

        $this->expectException(NotFoundException::class);

        call_user_func($this->getTestable(), new GetDepartmentByIdQuery('test'));
    }

    private function getTestable(): GetDepartmentByIdHandler
    {
        return new GetDepartmentByIdHandler(
            $this->departmentReadModel,
            $this->createMock(LoggerInterface::class)
        );
    }
}
