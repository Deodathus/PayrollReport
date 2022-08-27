<?php
declare(strict_types=1);

namespace PayrollReport\Shared\Application\Query;

final class Sort
{
    public function __construct(public readonly string $column, public readonly SortOrder $order) {}
}
