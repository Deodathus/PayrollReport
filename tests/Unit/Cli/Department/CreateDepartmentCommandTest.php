<?php
declare(strict_types=1);

namespace PayrollReport\Tests\Unit\Cli\Department;

use PayrollReport\Adapter\Driver\Cli\Department\CreateDepartmentCommand;
use PayrollReport\Shared\Application\Command\CommandBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class CreateDepartmentCommandTest extends TestCase
{
    private CommandTester $commandTester;

    private CommandBus|MockObject $commandBus;

    public function setUp(): void
    {
        $this->commandBus = $this->createMock(CommandBus::class);

        $application = new Application();
        $application->add(new CreateDepartmentCommand($this->commandBus));
        $command = $application->find('department:create');

        $this->commandTester = new CommandTester($command);
    }

    /** @test */
    public function shouldExecuteSuccessfully(): void
    {
        $resultCode = $this->commandTester->execute([
            'name' => 'test',
            'salaryBonus' => 500,
            'salaryBonusType' => 'percentage',
        ]);

        $this->assertEquals(Command::SUCCESS, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfNameAssertion(): void
    {
        $resultCode = $this->commandTester->execute([
            'name' => '',
            'salaryBonus' => 'hello',
            'salaryBonusType' => 'no',
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfSalaryAssertion(): void
    {
        $resultCode = $this->commandTester->execute([
            'name' => 'test',
            'salaryBonus' => 'hello',
            'salaryBonusType' => 'no',
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }

    /** @test */
    public function shouldExecuteWithFailureBecauseOfSalaryBonusAssertion(): void
    {
        $resultCode = $this->commandTester->execute([
            'name' => 'test',
            'salaryBonus' => 500,
            'salaryBonusType' => 'no',
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
            'name' => 'test',
            'salaryBonus' => 500,
            'salaryBonusType' => 'percentage',
        ]);

        $this->assertEquals(Command::FAILURE, $resultCode);
    }
}
