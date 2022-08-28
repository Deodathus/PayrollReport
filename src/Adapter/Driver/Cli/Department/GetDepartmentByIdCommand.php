<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driver\Cli\Department;

use Assert\LazyAssertionException;
use PayrollReport\Adapter\Driver\Cli\Department\Input\GetDepartmentByIdInput;
use PayrollReport\Modules\Department\Application\Department\Department;
use PayrollReport\Modules\Department\Application\Department\GetById\GetDepartmentByIdQuery;
use PayrollReport\Shared\Application\NotFoundException;
use PayrollReport\Shared\Application\Query\QueryBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

#[AsCommand('department:get-by-id', 'Returns department by ID')]
final class GetDepartmentByIdCommand extends Command
{
    private const DEPARTMENT_ID_ARGUMENT = 'departmentId';

    public function __construct(private readonly QueryBus $queryBus)
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $getDepartmentInput = GetDepartmentByIdInput::fromArray([
                self::DEPARTMENT_ID_ARGUMENT => $input->getArgument(self::DEPARTMENT_ID_ARGUMENT),
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
            /** @var Department $department */
            $department = $this->queryBus->handle(
                new GetDepartmentByIdQuery($getDepartmentInput->id)
            );
        } catch (HandlerFailedException $exception) {
            if ($exception->getPrevious() !== null && get_class($exception->getPrevious()) === NotFoundException::class) {
                $output->writeln(sprintf("<error>%s</error>", $exception->getPrevious()->getMessage()));
            } else {
                $output->writeln('<error>There was error occurred during process!</error>');
            }

            return Command::FAILURE;
        }

        $this->displayResult($department, $output);

        return Command::SUCCESS;
    }

    private function displayResult(Department $department, OutputInterface $output): void
    {
        $table = new Table($output);
        $table->setHeaders(['ID', 'Name', 'Salary Bonus Type', 'Salary Bonus']);
        $table->setRows([
            [$department->id, $department->name, $department->salaryBonusType, $department->salaryBonus],
        ]);
        $table->render();
    }
}
