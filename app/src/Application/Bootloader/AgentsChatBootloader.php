<?php

declare(strict_types=1);

namespace App\Application\Bootloader;

use App\Domain\Chat\PromptGenerator\SessionContextInjector;
use App\Domain\Chat\SimpleChatService;
use App\Infrastructure\RoadRunner\Chat\ChatHistoryRepository;
use LLM\Agents\Chat\ChatHistoryRepositoryInterface;
use LLM\Agents\Chat\ChatServiceInterface;
use LLM\Agents\PromptGenerator\Interceptors\AgentMemoryInjector;
use LLM\Agents\PromptGenerator\Interceptors\InstructionGenerator;
use LLM\Agents\PromptGenerator\Interceptors\LinkedAgentsInjector;
use LLM\Agents\PromptGenerator\Interceptors\UserPromptInjector;
use LLM\Agents\PromptGenerator\PromptGeneratorPipeline;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Cache\CacheStorageProviderInterface;

final class AgentsChatBootloader extends Bootloader
{
    public function defineSingletons(): array
    {
        return [
            ChatServiceInterface::class => SimpleChatService::class,
            ChatHistoryRepositoryInterface::class => static fn(
                CacheStorageProviderInterface $cache,
            ): ChatHistoryRepositoryInterface => new ChatHistoryRepository(
                $cache->storage('chat-messages'),
            ),

            PromptGeneratorPipeline::class => static function (
                LinkedAgentsInjector $linkedAgentsInjector,
            ): PromptGeneratorPipeline {
                $pipeline = new PromptGeneratorPipeline();

                return $pipeline->withInterceptor(
                    new InstructionGenerator(),
                    new AgentMemoryInjector(),
                    $linkedAgentsInjector,
                    new SessionContextInjector(),
                    new UserPromptInjector(),
                );
            },
        ];
    }
}
