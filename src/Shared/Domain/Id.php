<?php
declare(strict_types=1);

namespace PayrollReport\Shared\Domain;

use Ramsey\Uuid\Uuid;

abstract class Id
{
    private function __construct(private readonly string $id) {}

    public static function fromString(?string $id): ?static
    {
        if ($id === null) {
            return null;
        }

        return new static($id);
    }

    public static function generate(): static
    {
        return new static(Uuid::uuid4()->toString());
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
