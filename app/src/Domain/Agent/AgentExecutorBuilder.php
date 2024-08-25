<?php

declare(strict_types=1);

namespace App\Domain\Agent;

use LLM\Agents\Agent\AgentExecutor;
use LLM\Agents\Agent\Exception\InvalidBuilderStateException;
use LLM\Agents\Agent\Execution;
use LLM\Agents\LLM\Prompt\Chat\MessagePrompt;
use LLM\Agents\LLM\Prompt\Chat\Prompt;

final class AgentExecutorBuilder
{
    private ?Prompt $prompt = null;
    private ?string $agentKey = null;
    private array $sessionContext = [];

    public function __construct(
        private readonly AgentExecutor $executor,
    ) {}

    public function withPrompt(Prompt $prompt): self
    {
        $self = clone $this;
        $self->prompt = $prompt;

        return $self;
    }

    public function withAgentKey(string $agentKey): self
    {
        $self = clone $this;
        $self->agentKey = $agentKey;

        return $self;
    }

    public function withSessionContext(array $sessionContext): self
    {
        $self = clone $this;
        $self->sessionContext = $sessionContext;

        return $self;
    }

    public function withMessage(MessagePrompt $message): self
    {
        if ($this->prompt === null) {
            throw new InvalidBuilderStateException('Cannot add message without a prompt');
        }

        $this->prompt = $this->prompt->withAddedMessage($message);

        return $this;
    }

    public function ask(string|\Stringable $prompt): Execution
    {
        if ($this->agentKey === null) {
            throw new InvalidBuilderStateException('Agent key is required');
        }

        if ($this->prompt !== null) {
            $prompt = $this->prompt->withAddedMessage(
                MessagePrompt::user(
                    prompt: $prompt,
                ),
            );
        }

        $execution = $this->executor->execute(
            agent: $this->agentKey,
            prompt: $prompt,
            sessionContext: $this->sessionContext,
        );

        $this->prompt = $execution->prompt;

        return $execution;
    }

    public function continue(): Execution
    {
        if ($this->agentKey === null) {
            throw new InvalidBuilderStateException('Agent key is required');
        }

        $execution = $this->executor->execute(
            agent: $this->agentKey,
            prompt: $this->prompt,
            sessionContext: $this->sessionContext,
        );

        $this->prompt = $execution->prompt;

        return $execution;
    }

    public function __clone()
    {
        $this->prompt = null;
    }

    public function getPrompt(): ?Prompt
    {
        return $this->prompt;
    }
}
