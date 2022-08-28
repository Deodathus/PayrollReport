<?php
declare(strict_types=1);

namespace PayrollReport\Adapter\Driver\Cli\Report\Input;

use Assert\Assert;
use PayrollReport\Modules\Report\Application\Report\FilterableColumn;
use PayrollReport\Modules\Report\Application\Report\SortableColumn;
use PayrollReport\Shared\Application\Query\SortOrder;

final class GetReportByIdInput
{
    private function __construct(
        public readonly string $reportId,
        public readonly ?array $filters,
        public readonly ?string $sortBy,
        public readonly ?string $sortByOrder
    ) {}

    public static function fromArray(array $data): self
    {
        $reportId = $data['reportId'];
        $filters = $data['filter'];
        $sortBy = $data['sortBy'];
        $sortByOrder = $data['sortOrder'];

        $assertion = Assert::lazy()
            ->that($reportId, 'reportId')->string()->notBlank();

        foreach ($filters as $filter) {
            [$column, $value] = explode('=', $filter);

            $assertion
                ->that($column, 'column')->string()->notBlank()->inArray(
                    array_map(
                        static fn (FilterableColumn $column): string => $column->value,
                        FilterableColumn::cases()
                    )
                )
                ->that($value, 'value')->string()->notBlank();
        }

        if ($sortBy !== null && $sortByOrder !== null) {
            $assertion
                ->that($sortBy, 'sortBy')->string()->notBlank()->inArray(
                    array_map(
                        static fn (SortableColumn $column): string => $column->value,
                        SortableColumn::cases()
                    )
                )
                ->that($sortByOrder, 'sortByOrder')->string()->notBlank()->inArray(
                    array_map(
                        static fn (SortOrder $sortOrder): string => $sortOrder->value,
                        SortOrder::cases()
                    )
                );
        }

        $assertion->verifyNow();

        return new self(
            $reportId,
            $filters,
            $sortBy,
            $sortByOrder
        );
    }
}
