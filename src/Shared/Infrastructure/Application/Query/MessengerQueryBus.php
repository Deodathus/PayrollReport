<?php
declare(strict_types=1);

namespace PayrollReport\Shared\Infrastructure\Application\Query;

use PayrollReport\Shared\Application\Query\Query;
use PayrollReport\Shared\Application\Query\QueryBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class MessengerQueryBus implements QueryBus
{
    public function __construct(private readonly MessageBusInterface $queryBus) {}

    public function handle(Query $query): mixed
    {
        return $this->queryBus->dispatch($query)->last(HandledStamp::class)?->getResult();
    }
}
