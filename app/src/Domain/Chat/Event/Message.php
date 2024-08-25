<?php

declare(strict_types=1);

namespace App\Domain\Chat\Event;

use App\Application\Entity\Uuid;

final readonly class Message
{
    public Uuid $uuid;

    public function __construct(
        public Uuid $sessionUuid,
        public \DateTimeImmutable $createdAt,
        public string|\Stringable $message,
    ) {
        $this->uuid = Uuid::generate();
    }
}
