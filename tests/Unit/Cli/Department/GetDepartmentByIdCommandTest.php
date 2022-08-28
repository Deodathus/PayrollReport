<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Cli\Department;

use PayrollReport\Adapter\Driver\Cli\Department\GetDepartmentByIdCommand;
use PayrollReport\Modules\Department\Application\Department\Department;
use PayrollReport\Shared\Application\Query\QueryBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class GetDepartmentByIdCommandTest extends TestCase
{
    private CommandTester $commandTester;

    private QueryBus|MockObject $queryBus;

    public function setUp(): void
    {
        $this->queryBus = $this->createMock(QueryBus::class);

        $application = new Application();
        $application->add(new GetDepartmentByIdCommand($this->queryBus));
        $command = $application->find('department:get-by-id');

        $this->commandTester = new CommandTester($command);
    }

    /** @test */
    public function shouldExecuteSuccessfully(): void
    {
        $this->queryBus
            ->expects(self::once())
            ->method('handle')
            ->willReturn(
                new Department('test','test name','fixed_amount',500)
            );

        $resultCode = $this->commandTester->execute([
            'departmentId' => 'test',
        ]);

        $this->assertEquals(Command::SUCCESS, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfIdAssertion(): void
    {
        $resultCode = $this->commandTester->execute([
            'departmentId' => '',
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
            'departmentId' => 'test',
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }
}
