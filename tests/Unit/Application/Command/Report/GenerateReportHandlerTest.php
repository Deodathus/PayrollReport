<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Application\Command\Report;

use PayrollReport\Modules\Report\Application\Employee\Adapter\Employee;
use PayrollReport\Modules\Report\Application\Employee\Adapter\EmployeeFetcher;
use PayrollReport\Modules\Report\Application\Employee\Adapter\EmployeeFetcherInterface;
use PayrollReport\Modules\Report\Application\Report\Generate\GenerateReportCommand;
use PayrollReport\Modules\Report\Application\Report\Generate\GenerateReportHandler;
use PayrollReport\Modules\Report\Domain\ReportRepository;
use PayrollReport\Shared\Application\NotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GenerateReportHandlerTest extends TestCase
{
    private EmployeeFetcher|MockObject $employeeFetcher;

    private ReportRepository|MockObject $reportRepository;

    public function setUp(): void
    {
        $this->employeeFetcher = $this->createMock(EmployeeFetcherInterface::class);
        $this->reportRepository = $this->createMock(ReportRepository::class);
    }

    /** @test */
    public function shouldGenerateAndStoreReport(): void
    {
        $this->employeeFetcher->expects(self::once())->method('fetchAll')->willReturn([
            new Employee(
                'Test name',
                'Test name',
                'Test department name',
                500,
                500,
                'fixed_amount',
                1000
            )
        ]);
        $this->reportRepository->expects(self::once())->method('store');

        call_user_func($this->getTestable(), new GenerateReportCommand());
    }

    /** @test */
    public function shouldThrowNotFoundException(): void
    {
        $this->employeeFetcher->expects(self::once())->method('fetchAll');
        $this->reportRepository->expects(self::never())->method('store');

        $this->expectException(NotFoundException::class);

        call_user_func($this->getTestable(), new GenerateReportCommand());
    }

    private function getTestable(): GenerateReportHandler
    {
        return new GenerateReportHandler(
            $this->employeeFetcher,
            $this->reportRepository,
        );
    }
}
