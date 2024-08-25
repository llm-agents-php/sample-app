<?php

declare(strict_types=1);

namespace App\Infrastructure\RoadRunner\Chat;

use App\Application\Entity\Uuid;
use App\Domain\Chat\ChatHistoryRepositoryInterface;
use Psr\SimpleCache\CacheInterface;

final readonly class ChatHistoryRepository implements ChatHistoryRepositoryInterface
{
    public function __construct(
        private CacheInterface $cache,
    ) {}

    public function getMessages(Uuid $sessionUuid): iterable
    {
        $messages = (array) $this->cache->get((string) $sessionUuid);

        foreach ($messages as $message) {
            yield $message;
        }
    }

    public function addMessage(Uuid $sessionUuid, object $message): void
    {
        $messages = (array) $this->cache->get((string) $sessionUuid);
        $messages[] = $message;

        $this->cache->set((string) $sessionUuid, $messages);
    }
}
