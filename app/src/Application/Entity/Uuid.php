<?php

declare(strict_types=1);

namespace App\Application\Entity;

use Ramsey\Uuid\UuidInterface;

final readonly class Uuid implements \Stringable, \JsonSerializable
{
    public static function generate(): self
    {
        return new self(\Ramsey\Uuid\Uuid::uuid7());
    }

    public static function fromString(string $uuid): self
    {
        return new self(\Ramsey\Uuid\Uuid::fromString($uuid));
    }

    public function __construct(
        public UuidInterface $uuid,
    ) {}

    /** Create from data storage raw value */
    final public static function typecast(mixed $value): self
    {
        return self::fromString($value);
    }

    public function equals(Uuid $uuid): bool
    {
        return $this->uuid->equals($uuid->uuid);
    }

    public function __toString(): string
    {
        return $this->uuid->toString();
    }

    public function jsonSerialize(): string
    {
        return $this->uuid->toString();
    }
}
