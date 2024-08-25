<?php

declare(strict_types=1);

namespace App\Application\Bootloader;

use App\Application\AgentsLocator;
use App\Application\ToolsLocator;
use App\Domain\Chat\ChatHistoryRepositoryInterface;
use App\Domain\Chat\ChatServiceInterface;
use App\Domain\Chat\SimpleChatService;
use App\Domain\MLQ\AgentPromptGenerator;
use App\Domain\Tool\SchemaMapper;
use App\Infrastructure\OpenAI\ContextFactory;
use App\Infrastructure\OpenAI\OptionsFactory;
use App\Infrastructure\RoadRunner\Chat\ChatHistoryRepository;
use LLM\Agents\Agent\AgentRegistry;
use LLM\Agents\Agent\AgentRegistryInterface;
use LLM\Agents\Agent\AgentRepositoryInterface;
use LLM\Agents\LLM\AgentPromptGeneratorInterface;
use LLM\Agents\LLM\ContextFactoryInterface;
use LLM\Agents\LLM\OptionsFactoryInterface;
use LLM\Agents\Tool\SchemaMapperInterface;
use LLM\Agents\Tool\ToolRegistry;
use LLM\Agents\Tool\ToolRegistryInterface;
use LLM\Agents\Tool\ToolRepositoryInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Cache\CacheStorageProviderInterface;
use Spiral\Tokenizer\TokenizerListenerRegistryInterface;

final class AgentsBootloader extends Bootloader
{
    public function defineSingletons(): array
    {
        return [
            ToolRegistry::class => ToolRegistry::class,
            ToolRegistryInterface::class => ToolRegistry::class,
            ToolRepositoryInterface::class => ToolRegistry::class,

            AgentRegistry::class => AgentRegistry::class,
            AgentRegistryInterface::class => AgentRegistry::class,
            AgentRepositoryInterface::class => AgentRegistry::class,

            ChatServiceInterface::class => SimpleChatService::class,

            OptionsFactoryInterface::class => OptionsFactory::class,
            ContextFactoryInterface::class => ContextFactory::class,

            AgentPromptGeneratorInterface::class => AgentPromptGenerator::class,

            SchemaMapperInterface::class => SchemaMapper::class,

            ChatHistoryRepositoryInterface::class => static fn(
                CacheStorageProviderInterface $cache,
            ): ChatHistoryRepositoryInterface => new ChatHistoryRepository(
                $cache->storage('chat-messages'),
            ),
        ];
    }

    public function init(
        TokenizerListenerRegistryInterface $listenerRegistry,
        ToolsLocator $toolsLocator,
        AgentsLocator $agentsLocator,
    ): void {
        $listenerRegistry->addListener($agentsLocator);
        $listenerRegistry->addListener($toolsLocator);
    }
}
