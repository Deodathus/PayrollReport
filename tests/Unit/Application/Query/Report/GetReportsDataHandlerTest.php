<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Application\Query\Report;

use PayrollReport\Modules\Report\Application\Report\GetAllData\GetReportsDataHandler;
use PayrollReport\Modules\Report\Application\Report\GetAllData\GetReportsDataQuery;
use PayrollReport\Modules\Report\Application\Report\ReportData;
use PayrollReport\Modules\Report\Application\Report\ReportReadModel;
use PayrollReport\Modules\Report\Application\Report\ReportsData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetReportsDataHandlerTest extends TestCase
{
    private ReportReadModel|MockObject $reportReadModel;

    public function setUp(): void
    {
        $this->reportReadModel = $this->createMock(ReportReadModel::class);
    }

    /** @test */
    public function shouldReturnReportsData(): void
    {
        $reportsData = new ReportsData(...[
            new ReportData('test', 'now'),
            new ReportData('test-2', 'yesterday'),
        ]);

        $this->reportReadModel->expects(self::once())->method('fetchReportsData')->willReturn($reportsData);

        $result = call_user_func($this->getTestable(), new GetReportsDataQuery());

        $this->assertEquals($result, $reportsData);
    }

    private function getTestable(): GetReportsDataHandler
    {
        return new GetReportsDataHandler(
            $this->reportReadModel
        );
    }
}
