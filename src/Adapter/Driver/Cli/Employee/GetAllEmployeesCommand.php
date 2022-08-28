<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driver\Cli\Employee;

use PayrollReport\Modules\Department\Application\Employee\EmployeeViewModel;
use PayrollReport\Modules\Department\Application\Employee\EmployeesViewModels;
use PayrollReport\Modules\Department\Application\Employee\GetAll\GetAllEmployeesQuery;
use PayrollReport\Shared\Application\Query\QueryBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

#[AsCommand('employee:get-all', 'Returns all employees')]
class GetAllEmployeesCommand extends Command
{
    public function __construct(private readonly QueryBus $queryBus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            /** @var EmployeesViewModels $employees */
            $employees = $this->queryBus->handle(
                new GetAllEmployeesQuery()
            );
        } catch (HandlerFailedException $exception) {
            $output->writeln('<error>There was error occurred during process!</error>');

            return Command::FAILURE;
        }

        if ($employees->count() > 0) {
            $this->displayResult($employees, $output);
        } else {
            $output->writeln('<info>No employees found</info>');
        }

        return Command::SUCCESS;
    }

    private function displayResult(EmployeesViewModels $employees, OutputInterface $output): void
    {
        $table = new Table($output);
        $table->setHeaders(['ID', 'Department ID', 'First Name', 'Last Name', 'Hired At', 'Salary']);
        $table->setRows(
            array_map(
                static fn (EmployeeViewModel $employee): array => [
                    $employee->id,
                    $employee->departmentId,
                    $employee->firstName,
                    $employee->lastName,
                    $employee->hiredAt,
                    $employee->salary
                ],
                $employees->toArray()
            )
        );
        $table->render();
    }
}
