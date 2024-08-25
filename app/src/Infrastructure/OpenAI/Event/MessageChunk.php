<?php

declare(strict_types=1);

namespace App\Infrastructure\OpenAI\Event;

final readonly class MessageChunk
{
    public function __construct(
        public string $chunk,
        public bool $stop,
        public ?string $finishReason = null,
    ) {}
}
