<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driver\Cli\Report;

use PayrollReport\Modules\Report\Application\Report\Generate\GenerateReportCommand as GenerateReport;
use PayrollReport\Shared\Application\Command\CommandBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('report:generate', 'Generates report')]
final class GenerateReportCommand extends Command
{
    public function __construct(private readonly CommandBus $commandBus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->commandBus->dispatch(
                new GenerateReport()
            );
        } catch (\Exception $exception) {
            $output->writeln($exception->getMessage());

            return Command::FAILURE;
        }

        $output->writeln('<info>ReportPosition has been created!</info>');

        return Command::SUCCESS;
    }
}
