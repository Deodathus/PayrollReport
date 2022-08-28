<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Cli\Department;

use PayrollReport\Adapter\Driver\Cli\Department\GetAllDepartmentsCommand;
use PayrollReport\Modules\Department\Application\Department\Department;
use PayrollReport\Modules\Department\Application\Department\Departments;
use PayrollReport\Shared\Application\Query\QueryBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class GetAllDepartmentsCommandTest extends TestCase
{
    private CommandTester $commandTester;

    private QueryBus|MockObject $queryBus;

    public function setUp(): void
    {
        $this->queryBus = $this->createMock(QueryBus::class);

        $application = new Application();
        $application->add(new GetAllDepartmentsCommand($this->queryBus));
        $command = $application->find('department:get-all');

        $this->commandTester = new CommandTester($command);
    }

    /** @test */
    public function shouldExecuteSuccessfully(): void
    {
        $this->queryBus->expects(self::once())->method('handle')->willReturn(new Departments(...[
            new Department('test', 'test name', 'fixed_value', 500),
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
