<?php

declare(strict_types=1);

namespace App\Infrastructure\OpenAI;

use LLM\Agents\LLM\ContextInterface;
use LLM\Agents\LLM\LLMInterface;
use LLM\Agents\LLM\OptionsInterface;
use LLM\Agents\LLM\Prompt\Chat\MessagePrompt;
use LLM\Agents\LLM\Prompt\Chat\PromptInterface as ChatPromptInterface;
use LLM\Agents\LLM\Prompt\PromptInterface;
use LLM\Agents\LLM\Prompt\Tool;
use LLM\Agents\LLM\Response\Response;
use OpenAI\Contracts\ClientContract;

final class LLM implements LLMInterface
{
    protected array $defaultOptions = [
        Option::Temperature->value => 0.8,
        Option::MaxTokens->value => 120,
        Option::TopP->value => null,
        Option::FrequencyPenalty->value => null,
        Option::PresencePenalty->value => null,
        Option::Stop->value => null,
        Option::LogitBias->value => null,
        Option::FunctionCall->value => null,
        Option::Functions->value => null,
        Option::User->value => null,
        Option::Model->value => 'gpt-4o-mini',
    ];

    public function __construct(
        private readonly ClientContract $client,
        private readonly MessageMapper $messageMapper,
        protected readonly StreamResponseParser $streamParser,
    ) {}

    public function generate(
        ContextInterface $context,
        PromptInterface $prompt,
        OptionsInterface $options,
    ): Response {
        \assert($options instanceof Options);

        $request = $this->buildOptions($options);

        if ($prompt instanceof ChatPromptInterface) {
            $messages = $prompt->format();
        } else {
            $messages = [
                MessagePrompt::user($prompt)->toChatMessage(),
            ];
        }

        foreach ($messages as $message) {
            $request['messages'][] = $this->messageMapper->map($message);
        }

        if ($options->has(Option::Tools)) {
            $request['tools'] = \array_values(
                \array_map(
                    fn(Tool $tool): array => $this->messageMapper->map($tool),
                    $options->get(Option::Tools),
                ),
            );
        }

        $callback = null;
        if ($options->has(Option::StreamChunkCallback)) {
            $callback = $options->get(Option::StreamChunkCallback);
            \assert($callback instanceof StreamChunkCallbackInterface);
        }

        $stream = $this
            ->client
            ->chat()
            ->createStreamed($request);

        return $this->streamParser->parse($stream, $callback);
    }

    protected function buildOptions(OptionsInterface $options): array
    {
        $result = $this->defaultOptions;

        // only keys that present in default options should be replaced
        foreach ($options as $key => $value) {
            if (isset($this->defaultOptions[$key])) {
                $result[$key] = $value;
            }
        }

        if (!isset($result['model'])) {
            throw new \InvalidArgumentException('Model is required');
        }

        // filter out null options
        return \array_filter($result, static fn($value): bool => $value !== null);
    }
}
