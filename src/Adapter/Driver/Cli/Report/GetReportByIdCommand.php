<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driver\Cli\Report;

use Assert\LazyAssertionException;
use PayrollReport\Adapter\Driver\Cli\Report\Input\GetReportByIdInput;
use PayrollReport\Modules\Report\Application\Report\FilterableColumn;
use PayrollReport\Modules\Report\Application\Report\GetById\GetReportByIdQuery;
use PayrollReport\Modules\Report\Application\Report\Report;
use PayrollReport\Modules\Report\Application\Report\ReportPosition;
use PayrollReport\Modules\Report\Application\Report\SortableColumn;
use PayrollReport\Shared\Application\NotFoundException;
use PayrollReport\Shared\Application\Query\Filter;
use PayrollReport\Shared\Application\Query\Filters;
use PayrollReport\Shared\Application\Query\QueryBus;
use PayrollReport\Shared\Application\Query\Sort;
use PayrollReport\Shared\Application\Query\SortOrder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

#[AsCommand('report:get-by-id', 'Returns report')]
final class GetReportByIdCommand extends Command
{
    private const REPORT_ID_ARGUMENT = 'reportId';

    private const FILTER_OPTION = 'filter';

    private const SORT_BY_OPTION = 'sortBy';

    private const SORT_ORDER_OPTION = 'sortOrder';

    public function __construct(private readonly QueryBus $queryBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            self::REPORT_ID_ARGUMENT,
            InputArgument::REQUIRED,
            'Report\'s ID'
        );

        $filterableColumns = array_map(
            static fn (FilterableColumn $column): string => $column->value,
            FilterableColumn::cases()
        );
        $this->addOption(
            self::FILTER_OPTION,
            'f',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
            sprintf(
                'Filter syntax: example_column=example_value. Supported columns: <info>[%s]</info>',
                implode(', ', $filterableColumns)
            )
        );

        $sortableColumns = array_map(
            static fn (SortableColumn $column): string => $column->value,
            SortableColumn::cases()
        );
        $this->addOption(
            self::SORT_BY_OPTION,
            null,
            InputOption::VALUE_OPTIONAL,
            sprintf(
                'Sort by. Supported columns: <info>[%s]</info>',
                implode(', ', $sortableColumns)
            ),
            null
        );

        $supportedOrders = array_map(
            static fn (SortOrder $sortOrder): string => $sortOrder->value,
            SortOrder::cases()
        );
        $this->addOption(
            self::SORT_ORDER_OPTION,
            null,
            InputOption::VALUE_OPTIONAL,
            sprintf(
                'Sort order. Supported options: <info>[%s]</info>',
                implode(', ', $supportedOrders)
            ),
            null,
            $supportedOrders
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $getReportByIdInput = GetReportByIdInput::fromArray([
                self::REPORT_ID_ARGUMENT => $input->getArgument(self::REPORT_ID_ARGUMENT),
                self::FILTER_OPTION => $input->getOption(self::FILTER_OPTION),
                self::SORT_BY_OPTION => $input->getOption(self::SORT_BY_OPTION),
                self::SORT_ORDER_OPTION => $input->getOption(self::SORT_ORDER_OPTION),
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
            /** @var Report $report */
            $report = $this->queryBus->handle(
                new GetReportByIdQuery(
                    $getReportByIdInput->reportId,
                    $this->prepareFilters($getReportByIdInput->filters),
                    $this->prepareSort($getReportByIdInput->sortBy, $getReportByIdInput->sortByOrder)
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

        if ($report->count() > 0) {
            $this->displayResult($report, $output);
        } else {
            $output->writeln('<info>No report position found</info>');
        }

        return Command::SUCCESS;
    }

    private function prepareFilters(?array $inputFilters): Filters
    {
        $filters = [];

        if (count($inputFilters) > 0) {
            $filters = array_map(
                static function (string $filter): ?Filter {
                    [$column, $value] = explode('=', $filter);

                    return new Filter(
                        $column,
                        $value
                    );
                },
                $inputFilters
            );
        }

        return new Filters(...$filters);
    }

    private function prepareSort(?string $sortBy, ?string $sortOrder): ?Sort
    {
        return ($sortBy && $sortOrder) ? new Sort($sortBy, SortOrder::from($sortOrder)) : null;
    }

    private function displayResult(Report $report, OutputInterface $output): void
    {
        $table = new Table($output);
        $table->setHeaders([
            'First Name',
            'Last Name',
            'Department Name',
            'Base Salary',
            'Salary Bonus',
            'Salary Bonus Type',
            'Total Salary',
        ]);
        $table->setRows(
            array_map(
                static fn (ReportPosition $reportPosition): array => [
                    $reportPosition->employeeFirstName,
                    $reportPosition->employeeLastName,
                    $reportPosition->departmentName,
                    $reportPosition->baseSalary,
                    $reportPosition->salaryBonus,
                    $reportPosition->salaryBonusType,
                    $reportPosition->totalSalary
                ],
                $report->toArray()
            )
        );
        $table->render();
    }
}
