<?php

declare(strict_types=1);

namespace App\Domain\Chat\Event;

use App\Application\Entity\Uuid;

final readonly class MessageChunk extends Message
{
    public function __construct(
        Uuid $sessionUuid,
        \DateTimeImmutable $createdAt,
        \Stringable|string $message,
        public bool $isLast,
    ) {
        parent::__construct($sessionUuid, $createdAt, $message);
    }
}
