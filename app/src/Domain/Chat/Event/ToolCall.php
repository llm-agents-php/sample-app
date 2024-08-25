<?php

declare(strict_types=1);

namespace App\Domain\Chat\Event;

use App\Application\Entity\Uuid;

final readonly class ToolCall
{
    public function __construct(
        public Uuid $sessionUuid,
        public string $id,
        public string|\Stringable $tool,
        public string $arguments,
        public \DateTimeImmutable $createdAt,
    ) {}
}
