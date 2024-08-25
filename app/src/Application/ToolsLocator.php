<?php

declare(strict_types=1);

namespace App\Application;

use LLM\Agents\Tool\ToolInterface;
use LLM\Agents\Tool\ToolRegistryInterface;
use Spiral\Core\Container;
use Spiral\Tokenizer\Attribute\TargetClass;
use Spiral\Tokenizer\TokenizationListenerInterface;

#[TargetClass(ToolInterface::class)]
final readonly class ToolsLocator implements TokenizationListenerInterface
{
    public function __construct(
        private Container $container,
        private ToolRegistryInterface $tools,
    ) {}

    public function listen(\ReflectionClass $class): void
    {
        // Skip abstract classes and interfaces
        if (!$class->isInstantiable()) {
            return;
        }

        $tool = $this->container->make($class->getName());
        $this->tools->register($tool);
    }

    public function finalize(): void
    {
        // TODO: Implement finalize() method.
    }
}
