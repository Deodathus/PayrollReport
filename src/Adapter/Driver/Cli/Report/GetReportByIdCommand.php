<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driver\Cli\Report;

use PayrollReport\Modules\Report\Application\Report\GetById\GetReportByIdQuery;
use PayrollReport\Modules\Report\Application\Report\Report;
use PayrollReport\Modules\Report\Application\Report\ReportPosition;
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

#[AsCommand('report:get-by-id', 'Returns report')]
final class GetReportByIdCommand extends Command
{
    private const REPORT_ID_ARGUMENT = 'reportId';

    private const FILTER_OPTION = 'filter';

    private const SORT_BY_OPTION = 'sortBy';

    private const SORT_ORDER_OPTION = 'sortOrder';

    private const SUPPORTED_COLUMNS_FOR_FILTERS = [
        'employee_first_name',
        'employee_last_name',
        'department_name'
    ];

    private const SORTABLE_COLUMNS = [
        'employee_first_name',
        'employee_last_name',
        'department_name',
        'base_salary',
        'salary_bonus',
        'salary_bonus_type',
        'total_salary',
    ];

    private const SUPPORTED_SORT_ORDERS = [
        'ASC',
        'DESC',
    ];

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

        $this->addOption(
            self::FILTER_OPTION,
            'f',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
            sprintf(
                'Filter syntax: example_column=example_value. Supported columns: <info>[%s]</info>',
                implode(', ', self::SUPPORTED_COLUMNS_FOR_FILTERS)
            )
        );

        $this->addOption(
            self::SORT_BY_OPTION,
            null,
            InputOption::VALUE_OPTIONAL,
            sprintf(
                'Sort by. Supported columns: <info>[%s]</info>',
                implode(', ', self::SORTABLE_COLUMNS)
            ),
            null,
            self::SORTABLE_COLUMNS
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
        /** @var Report $report */
        $report = $this->queryBus->handle(
            new GetReportByIdQuery(
                $input->getArgument(self::REPORT_ID_ARGUMENT),
                $this->prepareFilters($input),
                $this->prepareSort($input)
            )
        );

        if ($report->count() > 0) {
            $this->displayResult($report, $output);
        } else {
            $output->writeln('<info>No report position found</info>');
        }

        return Command::SUCCESS;
    }

    private function prepareFilters(InputInterface $input): Filters
    {
        $filters = [];
        $inputFilters = $input->getOption(self::FILTER_OPTION);

        if (count($inputFilters) > 0) {
            $filters = array_map(
                static function (string $filter): ?Filter {
                    [$column, $value] = explode('=', $filter);

                    if (!in_array($column, self::SUPPORTED_COLUMNS_FOR_FILTERS)) {
                        return null;
                    }

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

    private function prepareSort(InputInterface $input): ?Sort
    {
        $sortBy = $input->getOption(self::SORT_BY_OPTION);
        $sortByOrder = $input->getOption(self::SORT_ORDER_OPTION);

        return
            ($sortBy && in_array($sortBy, self::SORTABLE_COLUMNS, true) &&
            $sortByOrder && in_array($sortByOrder, self::SUPPORTED_SORT_ORDERS, true)) ?
            new Sort($sortBy, SortOrder::from($sortByOrder)) :
            null;
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
