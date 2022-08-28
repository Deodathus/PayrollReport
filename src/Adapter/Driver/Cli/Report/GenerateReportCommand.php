<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driver\Cli\Report;

use PayrollReport\Modules\Report\Application\Report\Generate\GenerateReportCommand as GenerateReport;
use PayrollReport\Shared\Application\Command\CommandBus;
use PayrollReport\Shared\Application\NotFoundException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

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
        } catch (HandlerFailedException $exception) {
            if ($exception->getPrevious() !== null && get_class($exception->getPrevious()) === NotFoundException::class) {
                $output->writeln(sprintf("<error>%s</error>", $exception->getPrevious()->getMessage()));
            } else {
                $output->writeln('<error>There was error occurred during process!</error>');
            }

            return Command::FAILURE;
        }

        $output->writeln('<info>Report has been created!</info>');

        return Command::SUCCESS;
    }
}
