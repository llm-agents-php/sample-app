<?php

declare(strict_types=1);

namespace App\Agents\TaskSplitter;

use LLM\Agents\Agent\AgentFactoryInterface;
use LLM\Agents\Agent\AgentInterface;

final class TaskSplitterAgentFactory implements AgentFactoryInterface
{
    public function create(): AgentInterface
    {
        return TaskSplitterAgent::create();
    }
}
