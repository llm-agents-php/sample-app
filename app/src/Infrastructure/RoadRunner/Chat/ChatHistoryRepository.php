<?php

declare(strict_types=1);

namespace App\Infrastructure\RoadRunner\Chat;

use LLM\Agents\Chat\ChatHistoryRepositoryInterface;
use Psr\SimpleCache\CacheInterface;
use Ramsey\Uuid\UuidInterface;

final readonly class ChatHistoryRepository implements ChatHistoryRepositoryInterface
{
    public function __construct(
        private CacheInterface $cache,
    ) {}

    public function getMessages(UuidInterface $sessionUuid): iterable
    {
        $messages = (array) $this->cache->get((string) $sessionUuid);

        foreach ($messages as $message) {
            yield $message;
        }
    }

    public function addMessage(UuidInterface $sessionUuid, object $message): void
    {
        $messages = (array) $this->cache->get((string) $sessionUuid);
        $messages[] = $message;

        $this->cache->set((string) $sessionUuid, $messages);
    }

    public function clear(UuidInterface $sessionUuid): void
    {
        $this->cache->delete((string) $sessionUuid);
    }
}
