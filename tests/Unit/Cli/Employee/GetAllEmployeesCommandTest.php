<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Cli\Employee;

use PayrollReport\Adapter\Driver\Cli\Employee\GetAllEmployeesCommand;
use PayrollReport\Modules\Department\Application\Employee\EmployeesViewModels;
use PayrollReport\Modules\Department\Application\Employee\EmployeeViewModel;
use PayrollReport\Shared\Application\Query\QueryBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class GetAllEmployeesCommandTest extends TestCase
{
    private CommandTester $commandTester;

    private QueryBus|MockObject $queryBus;

    public function setUp(): void
    {
        $this->queryBus = $this->createMock(QueryBus::class);

        $application = new Application();
        $application->add(new GetAllEmployeesCommand($this->queryBus));
        $command = $application->find('employee:get-all');

        $this->commandTester = new CommandTester($command);
    }

    /** @test */
    public function shouldExecuteSuccessfully(): void
    {
        $this->queryBus->expects(self::once())->method('handle')->willReturn(new EmployeesViewModels(...[
            new EmployeeViewModel(
                'test',
                'test-id',
                'test',
                'test',
                'now',
                500
            ),
        ]));

        $resultCode = $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $resultCode);
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

        $resultCode = $this->commandTester->execute([]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }
}
