<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driver\Cli\Report;

use PayrollReport\Modules\Report\Application\Report\GetAllData\GetReportsDataQuery;
use PayrollReport\Modules\Report\Application\Report\ReportData;
use PayrollReport\Modules\Report\Application\Report\ReportsData;
use PayrollReport\Shared\Application\Query\QueryBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('report:get-all-data', 'Returns reports data such as ID and generated timestamp')]
final class GetReportsDataCommand extends Command
{
    public function __construct(private readonly QueryBus $queryBus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ReportsData $reportsData */
        $reportsData = $this->queryBus->handle(
            new GetReportsDataQuery()
        );

        if ($reportsData->count() > 0) {
            $this->displayResult($reportsData, $output);
        } else {
            $output->writeln('<info>No reports data found</info>');
        }

        return Command::SUCCESS;
    }

    private function displayResult(ReportsData $reportsData, OutputInterface $output): void
    {
        $table = new Table($output);
        $table->setHeaders(['Report ID', 'Generated At']);
        $table->setRows(
            array_map(
                static fn (ReportData $reportData): array => [
                    $reportData->id,
                    $reportData->generatedAt
                ],
                $reportsData->toArray()
            )
        );
        $table->render();
    }
}
