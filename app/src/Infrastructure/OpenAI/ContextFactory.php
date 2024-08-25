<?php

declare(strict_types=1);

namespace App\Infrastructure\OpenAI;

use LLM\Agents\LLM\ContextFactoryInterface;
use LLM\Agents\LLM\ContextInterface;

final class ContextFactory implements ContextFactoryInterface
{
    public function create(): ContextInterface
    {
        return new Context();
    }
}
