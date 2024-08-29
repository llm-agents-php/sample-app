<?php

declare(strict_types=1);

namespace App\Domain\MLQ;

use LLM\Agents\Agent\AgentInterface;
use LLM\Agents\Agent\AgentRepositoryInterface;
use LLM\Agents\LLM\AgentPromptGeneratorInterface;
use LLM\Agents\LLM\Prompt\Chat\ChatMessage;
use LLM\Agents\LLM\Prompt\Chat\MessagePrompt;
use LLM\Agents\LLM\Prompt\Chat\Prompt;
use LLM\Agents\LLM\Prompt\Chat\PromptInterface;
use LLM\Agents\LLM\Prompt\Chat\Role;
use LLM\Agents\Solution\AgentLink;
use LLM\Agents\Solution\SolutionMetadata;
use Spiral\JsonSchemaGenerator\Generator;

final readonly class AgentPromptGenerator implements AgentPromptGeneratorInterface
{
    public function __construct(
        private AgentRepositoryInterface $agents,
        private Generator $schemaGenerator,
    ) {}

    public function generate(
        AgentInterface $agent,
        string|\Stringable $prompt,
        ?array $sessionContext = null,
    ): PromptInterface {
        $messages = [
            // Top instruction
            MessagePrompt::system(
                prompt: \sprintf(
                    <<<'PROMPT'
{prompt}
Important rules:
- always response in markdown format
- think before responding to user
PROMPT,
                ),
            ),

            // Agent memory
            MessagePrompt::system(
                prompt: 'Instructions about your experiences, follow them: {memory}. And also {dynamic_memory}',
            ),
        ];

        $associatedAgents = \array_map(
            fn(AgentLink $agent): array => [
                'agent' => $this->agents->get($agent->getName()),
                'output_schema' => \json_encode($this->schemaGenerator->generate($agent->outputSchema)),
            ],
            $agent->getAgents(),
        );


        if (\count($associatedAgents) > 0) {
            $messages[] = MessagePrompt::system(
                prompt: <<<'PROMPT'
There are agents {associated_agents} associated with you. You can ask them for help if you need it.
Use the `ask_agent` tool and provide the agent key.
Always follow rules:
- Don't make up the agent key. Use only the ones from the provided list.
PROMPT,
            );
        }

        if ($sessionContext !== null) {
            $messages[] = MessagePrompt::system(
                prompt: 'Session context: {active_context}',
            );
        }

        // User prompt
        $messages[] = new ChatMessage(
            content: $prompt,
            role: Role::User,
        );

        return new Prompt(
            messages: $messages,
            variables: [
                'prompt' => $agent->getInstruction(),
                'active_context' => \json_encode($sessionContext),
                'associated_agents' => \implode(
                    PHP_EOL,
                    \array_map(
                        static fn(array $agent): string => \json_encode([
                            'key' => $agent['agent']->getKey(),
                            'description' => $agent['agent']->getDescription(),
                            'output_schema' => $agent['output_schema'],
                        ]),
                        $associatedAgents,
                    ),
                ),
                'memory' => \implode(
                    "\n",
                    \array_map(
                        static fn(SolutionMetadata $metadata) => $metadata->content,
                        $agent->getMemory(),
                    ),
                ),
            ],
        );
    }
}
