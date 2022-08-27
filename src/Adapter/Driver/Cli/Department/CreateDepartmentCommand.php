<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driver\Cli\Department;

use PayrollReport\Modules\Department\Application\Department\Store\StoreDepartmentCommand;
use PayrollReport\Modules\Department\Domain\Department\DepartmentSalaryBonusType;
use PayrollReport\Shared\Application\Command\CommandBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('department:create', 'Creates department with given data')]
final class CreateDepartmentCommand extends Command
{
    private const DEPARTMENT_NAME_ARGUMENT = 'name';

    private const DEPARTMENT_SALARY_BONUS_ARGUMENT = 'salaryBonus';

    private const DEPARTMENT_SALARY_BONUS_TYPE_ARGUMENT = 'salaryBonusType';

    public function __construct(private readonly CommandBus $commandBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            self::DEPARTMENT_NAME_ARGUMENT,
            InputArgument::REQUIRED,
            'Department\'s name'
        );

        $this->addArgument(
            self::DEPARTMENT_SALARY_BONUS_ARGUMENT,
            InputArgument::REQUIRED,
            'Department\'s salary bonus'
        );

        $supportedOptions = array_map(
            static fn (DepartmentSalaryBonusType $enum): string => $enum->value,
            DepartmentSalaryBonusType::cases()
        );
        $this->addArgument(
            self::DEPARTMENT_SALARY_BONUS_TYPE_ARGUMENT,
            InputArgument::REQUIRED,
            sprintf(
                'Department\'s salary bonus type. Supported options: [%s]',
                implode(', ', $supportedOptions)
            ),
            null,
            $supportedOptions
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->commandBus->dispatch(
            new StoreDepartmentCommand(
                $input->getArgument(self::DEPARTMENT_NAME_ARGUMENT),
                (float) $input->getArgument(self::DEPARTMENT_SALARY_BONUS_ARGUMENT),
                $input->getArgument(self::DEPARTMENT_SALARY_BONUS_TYPE_ARGUMENT)
            )
        );

        return Command::SUCCESS;
    }
}
