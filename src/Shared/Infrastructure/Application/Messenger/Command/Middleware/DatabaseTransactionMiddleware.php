<?php
declare(strict_types=1);

namespace PayrollReport\Shared\Infrastructure\Application\Messenger\Command\Middleware;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Throwable;

final class DatabaseTransactionMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly Connection $connection) {}

    /**
     * @throws Throwable
     * @throws Exception
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $this->connection->beginTransaction();

        try {
            $envelope = $stack->next()->handle($envelope, $stack);

            $this->connection->commit();

            return $envelope;
        } catch (Throwable $exception) {
            $this->connection->rollBack();

            if ($exception instanceof HandlerFailedException) {
                throw new HandlerFailedException(
                    $exception->getEnvelope()->withoutAll(HandledStamp::class),
                    $exception->getNestedExceptions()
                );
            }

            throw $exception;
        }
    }
}
