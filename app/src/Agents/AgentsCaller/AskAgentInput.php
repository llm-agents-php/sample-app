<?php

declare(strict_types=1);

namespace App\Agents\AgentsCaller;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final class AskAgentInput
{
    public function __construct(
        #[Field(title: 'Agent Name', description: 'The name of the agent to ask.')]
        public string $name,
        #[Field(title: 'Question', description: 'The question to ask the agent.')]
        public string $question,
        #[Field(title: 'Output Schema', description: 'The schema of the output.')]
        public string $outputSchema,
    ) {}
}
