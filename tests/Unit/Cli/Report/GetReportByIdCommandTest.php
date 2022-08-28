<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Cli\Report;

use PayrollReport\Adapter\Driver\Cli\Report\GetReportByIdCommand;
use PayrollReport\Modules\Report\Application\Report\Report;
use PayrollReport\Modules\Report\Application\Report\ReportPosition;
use PayrollReport\Shared\Application\Query\QueryBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class GetReportByIdCommandTest extends TestCase
{
    private CommandTester $commandTester;

    private QueryBus|MockObject $queryBus;

    public function setUp(): void
    {
        $this->queryBus = $this->createMock(QueryBus::class);

        $application = new Application();
        $application->add(new GetReportByIdCommand($this->queryBus));
        $command = $application->find('report:get-by-id');

        $this->commandTester = new CommandTester($command);
    }

    /** @test */
    public function shouldExecuteSuccessfully(): void
    {
        $this->queryBus
            ->expects(self::once())
            ->method('handle')
            ->willReturn(
                new Report(...[
                    new ReportPosition(
                        'test first name',
                        'test last name',
                        'department name',
                        500,
                        300,
                        'fixed_amount',
                        800
                    )
                ])
            );

        $resultCode = $this->commandTester->execute([
            'reportId' => 'test',
        ]);

        $this->assertEquals(Command::SUCCESS, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfIdAssertion(): void
    {
        $resultCode = $this->commandTester->execute([
            'reportId' => '',
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfFilterAssertion(): void
    {
        $resultCode = $this->commandTester->execute([
            'reportId' => 'test',
            '-f' => ['employuee_last_name=test'],
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfSortByAssertion(): void
    {
        $resultCode = $this->commandTester->execute([
            'reportId' => 'test',
            '--sortBy' => 'employea_last_name',
            '--sortOrder' => 'DESC',
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfSortOrderAssertion(): void
    {
        $resultCode = $this->commandTester->execute([
            'reportId' => 'test',
            '--sortBy' => 'employee_last_name',
            '--sortOrder' => 'ASCa',
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfHandlerException(): void
    {
        $this->queryBus->expects(self::once())
            ->method('handle')
            ->willThrowException(
                new HandlerFailedException(
                    new Envelope((object) '', []),
                    [new \RuntimeException()])
            );

        $resultCode = $this->commandTester->execute([
            'reportId' => 'test',
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }
}
