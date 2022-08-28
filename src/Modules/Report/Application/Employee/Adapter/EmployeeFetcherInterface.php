<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Application\Employee\Adapter;

interface EmployeeFetcherInterface
{
    /**
     * @return Employee[]
     */
    public function fetchAll(): array;
}
