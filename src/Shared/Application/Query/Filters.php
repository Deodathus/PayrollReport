<?php
declare(strict_types=1);

namespace PayrollReport\Shared\Application\Query;

final class Filters
{
    private array $filters;

    public function __construct(Filter ...$filter)
    {
        $this->filters = $filter;
    }

    public function toArray(): array
    {
        return $this->filters;
    }
}
