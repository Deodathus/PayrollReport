<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driver\Cli\Employee;

use DateTimeImmutable;
use Exception;
use PayrollReport\Modules\Department\Application\Employee\Store\StoreEmployeeCommand;
use PayrollReport\Shared\Application\Command\CommandBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('employee:create', 'Creates employee with given data')]
final class CreateEmployeeCommand extends Command
{
    private const DEPARTMENT_ID_ARGUMENT = 'departmentId';

    private const EMPLOYEE_FIRST_NAME_ARGUMENT = 'firstName';

    private const EMPLOYEE_LAST_NAME_ARGUMENT = 'lastName';

    private const EMPLOYEE_HIRED_AT_ARGUMENT = 'hiredAt';

    private const EMPLOYEE_SALARY_ARGUMENT = 'salary';

    public function __construct(
        private readonly CommandBus $commandBus,
        string $name = null
    ) {
        parent::__construct($name);
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
            'Employee hire date. Format YYYYmmdd.',
            'now'
        );
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->commandBus->dispatch(
            new StoreEmployeeCommand(
                $input->getArgument(self::DEPARTMENT_ID_ARGUMENT),
                $input->getArgument(self::EMPLOYEE_FIRST_NAME_ARGUMENT),
                $input->getArgument(self::EMPLOYEE_LAST_NAME_ARGUMENT),
                new DateTimeImmutable($input->getArgument(self::EMPLOYEE_HIRED_AT_ARGUMENT)),
                (float) $input->getArgument(self::EMPLOYEE_SALARY_ARGUMENT)
            )
        );

        return Command::SUCCESS;
    }
}
