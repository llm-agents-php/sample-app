<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl;

use LLM\Agents\Agent\AgentFactoryInterface;
use LLM\Agents\Agent\AgentInterface;

final class SmartHomeControlAgentFactory implements AgentFactoryInterface
{
    public function create(): AgentInterface
    {
        return SmartHomeControlAgent::create();
    }
}
