<?php

declare(strict_types=1);

namespace App\Agents\Delivery;


use LLM\Agents\Agent\AgentFactoryInterface;
use LLM\Agents\Agent\AgentInterface;

final class DeliveryAgentFactory implements AgentFactoryInterface
{
    public function create(): AgentInterface
    {
        return DeliveryAgent::create();
    }
}
