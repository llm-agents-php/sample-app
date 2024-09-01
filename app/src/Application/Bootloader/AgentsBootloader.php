<?php

declare(strict_types=1);

namespace App\Application\Bootloader;

use App\Application\AgentsLocator;
use App\Application\ToolsLocator;
use LLM\Agents\Agent\AgentRegistry;
use LLM\Agents\Agent\AgentRegistryInterface;
use LLM\Agents\Agent\AgentRepositoryInterface;
use LLM\Agents\JsonSchema\Mapper\SchemaMapper;
use LLM\Agents\LLM\ContextFactoryInterface;
use LLM\Agents\LLM\OptionsFactoryInterface;
use LLM\Agents\OpenAI\Client\ContextFactory;
use LLM\Agents\OpenAI\Client\OptionsFactory;
use LLM\Agents\Tool\SchemaMapperInterface;
use LLM\Agents\Tool\ToolRegistry;
use LLM\Agents\Tool\ToolRegistryInterface;
use LLM\Agents\Tool\ToolRepositoryInterface;
use Spiral\Boot\Bootloader\Bootloader;
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

            OptionsFactoryInterface::class => OptionsFactory::class,
            ContextFactoryInterface::class => ContextFactory::class,

            SchemaMapperInterface::class => SchemaMapper::class,
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
