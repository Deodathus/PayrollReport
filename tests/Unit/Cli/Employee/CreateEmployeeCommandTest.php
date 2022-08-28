<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Cli\Employee;

use PayrollReport\Adapter\Driver\Cli\Employee\CreateEmployeeCommand;
use PayrollReport\Shared\Application\Command\CommandBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class CreateEmployeeCommandTest extends TestCase
{
    private CommandTester $commandTester;

    private CommandBus|MockObject $commandBus;

    public function setUp(): void
    {
        $this->commandBus = $this->createMock(CommandBus::class);

        $application = new Application();
        $application->add(new CreateEmployeeCommand($this->commandBus));
        $command = $application->find('employee:create');

        $this->commandTester = new CommandTester($command);
    }

    /** @test */
    public function shouldExecuteSuccessfully(): void
    {
        $resultCode = $this->commandTester->execute([
            'departmentId' => 'test',
            'firstName' => 'test',
            'lastName' => 'test',
            'hiredAt' => 'now',
            'salary' => 500
        ]);

        $this->assertEquals(Command::SUCCESS, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfDepartmentIdAssertion(): void
    {
        $resultCode = $this->commandTester->execute([
            'departmentId' => '',
            'firstName' => 'test',
            'lastName' => 'test',
            'hiredAt' => 'now',
            'salary' => 500
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfFirstNameAssertion(): void
    {
        $resultCode = $this->commandTester->execute([
            'departmentId' => 'test',
            'firstName' => '',
            'lastName' => 'test',
            'hiredAt' => 'now',
            'salary' => 500
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfLastNameAssertion(): void
    {
        $resultCode = $this->commandTester->execute([
            'departmentId' => 'test',
            'firstName' => 'test',
            'lastName' => '',
            'hiredAt' => 'now',
            'salary' => 500
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfSalaryIsZeroAssertion(): void
    {
        $resultCode = $this->commandTester->execute([
            'departmentId' => 'test',
            'firstName' => 'test',
            'lastName' => 'test',
            'hiredAt' => 'now',
            'salary' => 0
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfSalaryIsNotNumericAssertion(): void
    {
        $resultCode = $this->commandTester->execute([
            'departmentId' => 'test',
            'firstName' => 'test',
            'lastName' => 'test',
            'hiredAt' => 'now',
            'salary' => 'test'
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfHiredAtAssertion(): void
    {
        $resultCode = $this->commandTester->execute([
            'departmentId' => 'test',
            'firstName' => 'test',
            'lastName' => 'test',
            'hiredAt' => '09123029',
            'salary' => 500
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
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

        $resultCode = $this->commandTester->execute([
            'departmentId' => 'test',
            'firstName' => 'test',
            'lastName' => 'test',
            'hiredAt' => 'now',
            'salary' => 500
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }
}
