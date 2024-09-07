<?php

declare(strict_types=1);

namespace App\Agents\Delivery;

use App\Agents\AgentsCaller\AskAgentTool;
use LLM\Agents\Agent\Agent;
use LLM\Agents\Agent\AgentAggregate;
use LLM\Agents\Agent\SiteStatusChecker\SiteStatusCheckerAgent;
use LLM\Agents\OpenAI\Client\OpenAIModel;
use LLM\Agents\Solution\AgentLink;
use LLM\Agents\Solution\MetadataType;
use LLM\Agents\Solution\Model;
use LLM\Agents\Solution\SolutionMetadata;
use LLM\Agents\Solution\ToolLink;

final class DeliveryAgent extends AgentAggregate
{
    public const NAME = 'order_assistant';

    public static function create(): self
    {
        $agent = new Agent(
            key: self::NAME,
            name: 'Order Assistant',
            description: 'Helps customers with order-related questions.',
            instruction: <<<'INSTRUCTION'
You are a helpful customer support assistant.
Use the supplied tools to assist the user.
INSTRUCTION
,
        );

        $aggregate = new self($agent);

        // Add some refiners to make the agent smarter
        $aggregate->addMetadata(
        // Memory
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'order_tip',
                content: 'First, retrieve the customer profile to provide personalized service.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'order_tip_server',
                content: 'Always check the server [google.com] status before providing any information.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'order_tip_repeat',
                content: 'Don\'t repeat the same information to the customer. If you have already provided the order number, don\'t repeat it. Provide only new information.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'order_tip_age',
                content: 'Tone of conversation is important, pay attention on age and fit the conversation to the age of the customer.',
            ),

            // Prompt
            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'server_status',
                content: 'Check the server [google.com] status to ensure that the system is operational.',
            ),
            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'what_is_order_number',
                content: 'What is my order number?',
            ),
            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'when_is_delivery',
                content: 'When will my order be delivered?',
            ),
            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'my_profile',
                content: 'Can you tell me more about my profile?',
            ),
        );

        // Add a model to the agent
        $model = new Model(model: OpenAIModel::Gpt4oMini->value);
        $aggregate->addAssociation($model);

        $aggregate->addAssociation(new ToolLink(name: GetDeliveryDateTool::NAME));
        $aggregate->addAssociation(new ToolLink(name: GetOrderNumberTool::NAME));
        $aggregate->addAssociation(new ToolLink(name: GetProfileTool::NAME));

        $aggregate->addAssociation(new ToolLink(name: AskAgentTool::NAME));
        $aggregate->addAssociation(
            new AgentLink(
                name: SiteStatusCheckerAgent::NAME,
                outputSchema: StatusCheckOutput::class,
            ),
        );

        return $aggregate;
    }
}
