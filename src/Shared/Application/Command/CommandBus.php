<?php
declare(strict_types=1);

namespace PayrollReport\Shared\Application\Command;

interface CommandBus
{
    public function dispatch(Command $command): void;
}
