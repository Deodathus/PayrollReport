<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driver\Cli\Employee;

use Assert\LazyAssertionException;
use DateTimeImmutable;
use Exception;
use PayrollReport\Adapter\Driver\Cli\Employee\Input\CreateEmployeeInput;
use PayrollReport\Modules\Department\Application\Employee\Store\StoreEmployeeCommand;
use PayrollReport\Shared\Application\Command\CommandBus;
use PayrollReport\Shared\Application\NotFoundException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

#[AsCommand('employee:create', 'Creates employee with given data')]
final class CreateEmployeeCommand extends Command
{
    private const DEPARTMENT_ID_ARGUMENT = 'departmentId';

    private const EMPLOYEE_FIRST_NAME_ARGUMENT = 'firstName';

    private const EMPLOYEE_LAST_NAME_ARGUMENT = 'lastName';

    private const EMPLOYEE_HIRED_AT_ARGUMENT = 'hiredAt';

    private const EMPLOYEE_SALARY_ARGUMENT = 'salary';

    public function __construct(private readonly CommandBus $commandBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            self::DEPARTMENT_ID_ARGUMENT,
            InputArgument::REQUIRED,
            'Department\'s ID'
        );

        $this->addArgument(
            self::EMPLOYEE_FIRST_NAME_ARGUMENT,
            InputArgument::REQUIRED,
            'Employee\'s first name'
        );

        $this->addArgument(
            self::EMPLOYEE_LAST_NAME_ARGUMENT,
            InputArgument::REQUIRED,
            'Employee\'s last name'
        );

        $this->addArgument(
            self::EMPLOYEE_SALARY_ARGUMENT,
            InputArgument::REQUIRED,
            'Employee\'s salary'
        );

        $this->addArgument(
            self::EMPLOYEE_HIRED_AT_ARGUMENT,
            InputArgument::OPTIONAL,
            'Employee hire date. Format: <info>[Ymd]</info>.',
            'now'
        );
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $employeeInput = CreateEmployeeInput::fromArray([
                self::DEPARTMENT_ID_ARGUMENT => $input->getArgument(self::DEPARTMENT_ID_ARGUMENT),
                self::EMPLOYEE_FIRST_NAME_ARGUMENT => $input->getArgument(self::EMPLOYEE_FIRST_NAME_ARGUMENT),
                self::EMPLOYEE_LAST_NAME_ARGUMENT => $input->getArgument(self::EMPLOYEE_LAST_NAME_ARGUMENT),
                self::EMPLOYEE_HIRED_AT_ARGUMENT => $input->getArgument(self::EMPLOYEE_HIRED_AT_ARGUMENT),
                self::EMPLOYEE_SALARY_ARGUMENT => $input->getArgument(self::EMPLOYEE_SALARY_ARGUMENT),
            ]);
        } catch (LazyAssertionException $exception) {
            $output->writeln(
                sprintf(
                    '<error>%s</error>',
                    $exception->getMessage()
                )
            );

            return Command::FAILURE;
        }

        try {
            $this->commandBus->dispatch(
                new StoreEmployeeCommand(
                    $employeeInput->departmentId,
                    $employeeInput->firstName,
                    $employeeInput->lastName,
                    $employeeInput->hiredAt,
                    $employeeInput->salary
                )
            );
        } catch (HandlerFailedException $exception) {
            if ($exception->getPrevious() !== null && get_class($exception->getPrevious()) === NotFoundException::class) {
                $output->writeln(sprintf("<error>%s</error>", $exception->getPrevious()->getMessage()));
            } else {
                $output->writeln('<error>There was error occurred during process!</error>');
            }

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
