<?php

declare(strict_types=1);

namespace App\Agents\CodeReviewer;

use LLM\Agents\Agent\Agent;
use LLM\Agents\Agent\AgentAggregate;
use LLM\Agents\OpenAI\Client\OpenAIModel;
use LLM\Agents\OpenAI\Client\Option;
use LLM\Agents\Solution\MetadataType;
use LLM\Agents\Solution\Model;
use LLM\Agents\Solution\SolutionMetadata;
use LLM\Agents\Solution\ToolLink;

final class CodeReviewAgent extends AgentAggregate
{
    public static function create(): self
    {
        $agent = new Agent(
            key: 'code_review',
            name: 'Code Reviewer',
            description: 'Agent can list files in project with given id and then open each file and review the code',
            instruction: 'You are a code review assistant. Use the provided tools to list project files, read their contents, and submit a code review for each file.',
        );

        $aggregate = new self($agent);

        $aggregate->addMetadata(
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'user_submitted_code_review',
                content: 'Always submit code reviews using proper tool.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'code_review_tip',
                content: 'Always submit constructive feedback and suggestions for improvement in your code reviews.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'customer_name',
                content: 'If you know the customer name say hello to them.',
            ),
            new SolutionMetadata(
                type: MetadataType::Configuration,
                key: Option::MaxTokens->value,
                content: 3000,
            ),
        );

        $model = new Model(model: OpenAIModel::Gpt4oMini->value);
        $aggregate->addAssociation($model);

        $aggregate->addAssociation(new ToolLink(name: ListProjectTool::NAME));
        $aggregate->addAssociation(new ToolLink(name: ReadFileTool::NAME));
        $aggregate->addAssociation(new ToolLink(name: ReviewTool::NAME));

        return $aggregate;
    }
}
