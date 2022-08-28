<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Cli\Report;

use PayrollReport\Adapter\Driver\Cli\Report\GenerateReportCommand;
use PayrollReport\Shared\Application\Command\CommandBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class GenerateReportCommandTest extends TestCase
{
    private CommandTester $commandTester;

    private CommandBus|MockObject $commandBus;

    public function setUp(): void
    {
        $this->commandBus = $this->createMock(CommandBus::class);

        $application = new Application();
        $application->add(new GenerateReportCommand($this->commandBus));
        $command = $application->find('report:generate');

        $this->commandTester = new CommandTester($command);
    }

    /** @test */
    public function shouldExecuteSuccessfully(): void
    {
        $resultCode = $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfHandlerException(): void
    {
        $this->commandBus->expects(self::once())
            ->method('dispatch')
            ->willThrowException(
                new HandlerFailedException(
                    new Envelope((object) '', []),
                    [new \RuntimeException()])
            );

        $resultCode = $this->commandTester->execute([]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }
}
