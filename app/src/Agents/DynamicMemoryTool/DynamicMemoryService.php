<?php

declare(strict_types=1);

namespace App\Agents\DynamicMemoryTool;

use App\Application\Entity\Uuid;
use LLM\Agents\Solution\SolutionMetadata;
use Psr\SimpleCache\CacheInterface;
use Spiral\Core\Attribute\Singleton;

#[Singleton]
final readonly class DynamicMemoryService
{
    public function __construct(
        private CacheInterface $cache,
    ) {}

    public function addMemory(Uuid $sessionUuid, SolutionMetadata $metadata): void
    {
        $memories = $this->getCurrentMemory($sessionUuid);

        $memories->addMemory($metadata);

        $this->cache->set($this->getKey($sessionUuid), $memories);
    }

    public function updateMemory(Uuid $sessionUuid, SolutionMetadata $metadata): void
    {
        $memories = $this->getCurrentMemory($sessionUuid);
        $memories->updateMemory($metadata);

        $this->cache->set($this->getKey($sessionUuid), $memories);
    }

    public function getCurrentMemory(Uuid $sessionUuid): Memories
    {
        return $this->cache->get($this->getKey($sessionUuid)) ?? new Memories(
            \Ramsey\Uuid\Uuid::uuid4(),
        );
    }

    private function getKey(Uuid $sessionUuid): string
    {
        return 'user_memory';
    }
}
