<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driver\Cli\Department;

use PayrollReport\Modules\Department\Application\Department\Department;
use PayrollReport\Modules\Department\Application\Department\Departments;
use PayrollReport\Modules\Department\Application\Department\GetAll\GetAllDepartmentsQuery;
use PayrollReport\Shared\Application\Query\QueryBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('department:get-all', 'Returns all departments')]
final class GetAllDepartmentsCommand extends Command
{
    public function __construct(private readonly QueryBus $queryBus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Departments $departments */
        $departments = $this->queryBus->handle(
            new GetAllDepartmentsQuery()
        );

        if ($departments->count() > 0) {
            $this->displayResult($departments, $output);
        } else {
            $output->writeln('<info>No departments found</info>');
        }

        return Command::SUCCESS;
    }

    private function displayResult(Departments $departments, OutputInterface $output): void
    {
        $table = new Table($output);
        $table->setHeaders(['ID', 'Name', 'Salary Bonus Type', 'Salary Bonus']);
        $table->setRows(
            array_map(
                static fn (Department $department): array => [
                    $department->id,
                    $department->name,
                    $department->salaryBonusType,
                    $department->salaryBonus
                ],
                $departments->toArray()
            )
        );
        $table->render();
    }
}
