<?php

declare(strict_types=1);

namespace App\Agents\SiteStatusChecker;

use LLM\Agents\Agent\AgentFactoryInterface;
use LLM\Agents\Agent\AgentInterface;

final class SiteStatusCheckerAgentFactory implements AgentFactoryInterface
{
    public function create(): AgentInterface
    {
        return SiteStatusCheckerAgent::create();
    }
}
