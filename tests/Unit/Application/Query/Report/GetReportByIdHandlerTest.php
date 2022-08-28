<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Application\Query\Report;

use PayrollReport\Modules\Report\Application\Report\GetById\GetReportByIdHandler;
use PayrollReport\Modules\Report\Application\Report\GetById\GetReportByIdQuery;
use PayrollReport\Modules\Report\Application\Report\Report;
use PayrollReport\Modules\Report\Application\Report\ReportPosition;
use PayrollReport\Modules\Report\Application\Report\ReportPositionReadModel;
use PayrollReport\Modules\Report\Domain\ReportRepository;
use PayrollReport\Shared\Application\NotFoundException;
use PayrollReport\Shared\Application\Query\Filters;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetReportByIdHandlerTest extends TestCase
{
    private ReportRepository|MockObject $reportRepository;

    private ReportPositionReadModel $reportPositionReadModel;

    public function setUp(): void
    {
        $this->reportRepository = $this->createMock(ReportRepository::class);
        $this->reportPositionReadModel = $this->createMock(ReportPositionReadModel::class);
    }

    /** @test */
    public function shouldReturnReport(): void
    {
        $this->reportRepository->expects(self::once())->method('existsById')->willReturn(true);

        $reportPositions = [
            new ReportPosition(
                'first name',
                'last name',
                'department name',
                500,
                300,
                'fixed_amount',
                800,
            ),
            new ReportPosition(
                'first name 2',
                'last name 2',
                'department name 2',
                600,
                300,
                'fixed_amount',
                900,
            ),
        ];
        $this->reportPositionReadModel
            ->expects(self::once())
            ->method('fetchByReportId')
            ->willReturn($reportPositions);

        $result = call_user_func($this->getTestable(), new GetReportByIdQuery('test', new Filters()));

        $this->assertEquals($result, new Report(...$reportPositions));
        $this->assertEquals($result->toArray(), $reportPositions);
    }

    /** @test */
    public function shouldThrowNotFoundException(): void
    {
        $this->reportRepository->expects(self::once())->method('existsById')->willReturn(false);

        $this->expectException(NotFoundException::class);

        call_user_func($this->getTestable(), new GetReportByIdQuery('test', new Filters()));
    }

    private function getTestable(): GetReportByIdHandler
    {
        return new GetReportByIdHandler(
            $this->reportRepository,
            $this->reportPositionReadModel
        );
    }
}
