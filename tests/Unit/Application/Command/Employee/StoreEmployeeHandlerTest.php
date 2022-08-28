<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Application\Command\Employee;

use DateTimeImmutable;
use PayrollReport\Modules\Department\Application\Employee\ApplicationException;
use PayrollReport\Modules\Department\Application\Employee\Store\StoreEmployeeCommand;
use PayrollReport\Modules\Department\Application\Employee\Store\StoreEmployeeHandler;
use PayrollReport\Modules\Department\Domain\Department\DepartmentRepository;
use PayrollReport\Modules\Department\Domain\Employee\EmployeeRepository;
use PayrollReport\Shared\Application\NotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class StoreEmployeeHandlerTest extends TestCase
{
    private EmployeeRepository|MockObject $employeeRepository;

    private DepartmentRepository|MockObject $departmentRepository;

    public function setUp(): void
    {
        $this->employeeRepository = $this->createMock(EmployeeRepository::class);
        $this->departmentRepository = $this->createMock(DepartmentRepository::class);
    }

    /** @test */
    public function shouldHandleAndStoreData(): void
    {
        $this->departmentRepository->expects(self::once())->method('existsWithId')->willReturn(true);
        $this->employeeRepository->expects(self::once())->method('store');

        call_user_func(
            $this->getTestable(),
            new StoreEmployeeCommand(
                'test_id',
                'Test first name',
                'Test last name',
                new DateTimeImmutable(),
                500
            )
        );
    }

    /** @test */
    public function shouldThrowNotFoundException(): void
    {
        $this->departmentRepository->expects(self::once())->method('existsWithId')->willReturn(false);
        $this->employeeRepository->expects(self::never())->method('store');

        $this->expectException(NotFoundException::class);

        call_user_func(
            $this->getTestable(),
            new StoreEmployeeCommand(
                'test_id',
                'Test first name',
                'Test last name',
                new DateTimeImmutable(),
                500
            )
        );
    }

    /** @test */
    public function shouldThrowApplicationException(): void
    {
        $this->departmentRepository->expects(self::once())->method('existsWithId')->willReturn(true);
        $this->employeeRepository->expects(self::never())->method('store');

        $this->expectException(ApplicationException::class);

        call_user_func(
            $this->getTestable(),
            new StoreEmployeeCommand(
                'test_id',
                'Test first name',
                'Test last name',
                new DateTimeImmutable(),
                0
            )
        );
    }

    private function getTestable(): StoreEmployeeHandler
    {
        return new StoreEmployeeHandler(
            $this->employeeRepository,
            $this->departmentRepository,
            $this->createMock(LoggerInterface::class)
        );
    }
}
