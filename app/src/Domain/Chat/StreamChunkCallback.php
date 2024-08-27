<?php

declare(strict_types=1);

namespace App\Domain\Chat;

use App\Application\Entity\Uuid;
use App\Domain\Chat\Event\MessageChunk;
use LLM\Agents\OpenAI\Client\StreamChunkCallbackInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class StreamChunkCallback implements StreamChunkCallbackInterface
{
    public function __construct(
        private Uuid $sessionUuid,
        private ?EventDispatcherInterface $eventDispatcher = null,
    ) {}

    public function __invoke(?string $chunk, bool $stop, ?string $finishReason = null): void
    {
        $this->eventDispatcher?->dispatch(
            new MessageChunk(
                sessionUuid: $this->sessionUuid,
                createdAt: new \DateTimeImmutable(),
                message: (string) $chunk,
                isLast: $stop,
            ),
        );
    }
}
