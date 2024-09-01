<?php

declare(strict_types=1);

namespace App\Application\Bootloader;

use App\Domain\Chat\SimpleChatService;
use App\Infrastructure\RoadRunner\Chat\ChatHistoryRepository;
use LLM\Agents\Chat\AgentPromptGenerator;
use LLM\Agents\Chat\ChatHistoryRepositoryInterface;
use LLM\Agents\Chat\ChatServiceInterface;
use LLM\Agents\LLM\AgentPromptGeneratorInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Cache\CacheStorageProviderInterface;

final class AgentsChatBootloader extends Bootloader
{
    public function defineSingletons(): array
    {
        return [
            ChatServiceInterface::class => SimpleChatService::class,
            AgentPromptGeneratorInterface::class => AgentPromptGenerator::class,
            ChatHistoryRepositoryInterface::class => static fn(
                CacheStorageProviderInterface $cache,
            ): ChatHistoryRepositoryInterface => new ChatHistoryRepository(
                $cache->storage('chat-messages'),
            ),
        ];
    }
}
