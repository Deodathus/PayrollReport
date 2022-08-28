<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Application\Command\Department;

use PayrollReport\Modules\Department\Application\Department\Store\StoreDepartmentCommand;
use PayrollReport\Modules\Department\Application\Department\Store\StoreDepartmentHandler;
use PayrollReport\Modules\Department\Application\Department\ApplicationException;
use PayrollReport\Modules\Department\Domain\Department\DepartmentRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ValueError;

final class StoreDepartmentHandlerTest extends TestCase
{
    private readonly DepartmentRepository|MockObject $departmentRepository;

    public function setUp(): void
    {
        $this->departmentRepository = $this->createMock(DepartmentRepository::class);
    }

    /** @test */
    public function shouldHandleAndStore(): void
    {
        $this->departmentRepository->expects(self::exactly(2))->method('store');

        call_user_func(
            $this->getTestable(),
            new StoreDepartmentCommand('Test', 500, 'percentage')
        );
        call_user_func(
            $this->getTestable(),
            new StoreDepartmentCommand('Test', 500, 'fixed_amount')
        );
    }

    /** @test */
    public function shouldThrowValueErrorException(): void
    {
        $this->departmentRepository->expects(self::never())->method('store');

        $this->expectException(ValueError::class);

        call_user_func(
            $this->getTestable(),
            new StoreDepartmentCommand('Test', 500, '1')
        );
    }

    /** @test */
    public function shouldThrowApplicationException(): void
    {
        $this->departmentRepository->expects(self::never())->method('store');

        $this->expectException(ApplicationException::class);

        call_user_func(
            $this->getTestable(),
            new StoreDepartmentCommand('Test', 0, 'percentage')
        );
    }

    private function getTestable(): StoreDepartmentHandler
    {
        return new StoreDepartmentHandler(
            $this->departmentRepository,
            $this->createMock(LoggerInterface::class)
        );
    }
}
