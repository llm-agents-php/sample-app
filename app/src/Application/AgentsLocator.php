<?php

declare(strict_types=1);

namespace App\Application;

use LLM\Agents\Agent\AgentFactoryInterface;
use LLM\Agents\Agent\AgentRegistryInterface;
use Spiral\Core\Container;
use Spiral\Tokenizer\Attribute\TargetClass;
use Spiral\Tokenizer\TokenizationListenerInterface;

#[TargetClass(AgentFactoryInterface::class)]
final readonly class AgentsLocator implements TokenizationListenerInterface
{
    public function __construct(
        private Container $container,
        private AgentRegistryInterface $agents,
    ) {}

    public function listen(\ReflectionClass $class): void
    {
        // Skip abstract classes and interfaces
        if (!$class->isInstantiable()) {
            return;
        }

        /** @var AgentFactoryInterface $factory */
        $factory = $this->container->make($class->getName());
        $this->agents->register($factory->create());
    }

    public function finalize(): void
    {
        // TODO: Implement finalize() method.
    }
}
