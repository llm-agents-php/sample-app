<?php

declare(strict_types=1);

namespace App\Agents\TaskSplitter;

use LLM\Agents\Agent\Agent;
use LLM\Agents\Agent\AgentAggregate;
use LLM\Agents\OpenAI\Client\OpenAIModel;
use LLM\Agents\OpenAI\Client\Option;
use LLM\Agents\Solution\MetadataType;
use LLM\Agents\Solution\Model;
use LLM\Agents\Solution\SolutionMetadata;
use LLM\Agents\Solution\ToolLink;

final class TaskSplitterAgent extends AgentAggregate
{
    public const NAME = 'task_splitter';

    public static function create(): self
    {
        $agent = new Agent(
            key: self::NAME,
            name: 'Task Splitter',
            description: 'An agent that splits project descriptions into structured task lists.',
            instruction: <<<'INSTRUCTION'
You are a task organization assistant.

Your primary goal is to analyze project descriptions and break them down into well-structured task lists with subtasks.
INSTRUCTION
            ,
        );

        $aggregate = new self($agent);

        $aggregate->addMetadata(
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'task_organization_tip',
                content: 'Always aim to create a logical hierarchy of tasks and subtasks. Main tasks should be broad objectives, while subtasks should be specific, actionable items.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'efficiency_tip',
                content: 'Try to keep the number of main tasks between 3 and 7 for better manageability. Break down complex tasks into subtasks when necessary.',
            ),

            // Prompts examples
            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'split_task',
                content: 'Split the project description f47ac10b-58cc-4372-a567-0e02b2c3d479 into a list of tasks and subtasks.',
            ),
            new SolutionMetadata(
                type: MetadataType::Configuration,
                key: Option::MaxTokens->value,
                content: 3000,
            ),
        );

        $model = new Model(model: OpenAIModel::Gpt4oMini->value);
        $aggregate->addAssociation($model);

        $aggregate->addAssociation(new ToolLink(name: TaskCreateTool::NAME));
        $aggregate->addAssociation(new ToolLink(name: GetProjectDescription::NAME));

        return $aggregate;
    }
}
