<?php

declare(strict_types=1);

namespace App\Agents\CodeReviewer;

use LLM\Agents\Agent\AgentFactoryInterface;
use LLM\Agents\Agent\AgentInterface;

final class CodeReviewAgentFactory implements AgentFactoryInterface
{
    public function create(): AgentInterface
    {
        return CodeReviewAgent::create();
    }
}
