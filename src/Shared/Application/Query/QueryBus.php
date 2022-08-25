<?php
declare(strict_types=1);

namespace PayrollReport\Shared\Application\Query;

interface QueryBus
{
    public function handle(Query $query): mixed;
}
