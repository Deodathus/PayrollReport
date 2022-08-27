<?php
declare(strict_types=1);

namespace PayrollReport\Shared\Application\Query;

final class Filter
{
    public function __construct(
        public readonly string $column,
        public readonly string $value
    ) {}
}
