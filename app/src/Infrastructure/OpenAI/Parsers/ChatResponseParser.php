<?php

declare(strict_types=1);

namespace App\Infrastructure\OpenAI\Parsers;

use App\Infrastructure\OpenAI\Event\MessageChunk;
use App\Infrastructure\OpenAI\StreamChunkCallbackInterface;
use LLM\Agents\LLM\Response\FinishReason;
use LLM\Agents\LLM\Response\Response;
use LLM\Agents\LLM\Response\StreamChatResponse;
use LLM\Agents\LLM\Response\ToolCall;
use LLM\Agents\LLM\Response\ToolCalledResponse;
use OpenAI\Contracts\ResponseStreamContract;
use OpenAI\Responses\Chat\CreateStreamedResponse;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class ChatResponseParser implements ParserInterface
{
    public function __construct(
        private ?EventDispatcherInterface $eventDispatcher = null,
    ) {}

    public function parse(ResponseStreamContract $stream, ?StreamChunkCallbackInterface $callback = null): Response
    {
        $callback ??= static fn(?string $chunk, bool $stop, ?string $finishReason = null) => null;
        $result = '';
        $finishReason = null;
        /** @var ToolCall[] $toolCalls */
        $toolCalls = [];
        $toolIndex = 0;

        /** @var CreateStreamedResponse[] $stream */
        foreach ($stream as $chunk) {
            if ($chunk->choices[0]->finishReason !== null) {
                $callback(chunk: '', stop: true, finishReason: $chunk->choices[0]->finishReason);
                $this->eventDispatcher?->dispatch(
                    new MessageChunk(
                        chunk: '',
                        stop: true,
                        finishReason: $chunk->choices[0]->finishReason,
                    ),
                );

                $finishReason = FinishReason::from($chunk->choices[0]->finishReason);
                break;
            }

            if ($chunk->choices[0]->delta->role !== null) {
                // For single tool call
                if ($chunk->choices[0]->delta?->toolCalls !== []) {
                    foreach ($chunk->choices[0]->delta->toolCalls as $i => $toolCall) {
                        $toolCalls[$toolIndex] = new ToolCall(
                            id: $toolCall->id,
                            name: $toolCall->function->name,
                            arguments: '',
                        );
                    }
                }

                continue;
            }


            if ($chunk->choices[0]->delta?->toolCalls !== []) {
                foreach ($chunk->choices[0]->delta->toolCalls as $i => $toolCall) {
                    // For multiple tool calls
                    if ($toolCall->id !== null) {
                        $toolIndex++;
                        $toolCalls[$toolIndex] = new ToolCall(
                            id: $toolCall->id,
                            name: $toolCall->function->name,
                            arguments: '',
                        );
                        continue;
                    }

                    $toolCalls[$toolIndex] = $toolCalls[$toolIndex]->withArguments($toolCall->function->arguments);
                }
                continue;
            }

            $callback(chunk: $chunk->choices[0]->delta->content, stop: false);
            $this->eventDispatcher->dispatch(
                new MessageChunk(
                    chunk: $chunk->choices[0]->delta->content,
                    stop: false,
                    finishReason: $chunk->choices[0]->finishReason,
                ),
            );

            $result .= $chunk->choices[0]->delta->content;
        }

        foreach ($toolCalls as $toolCall) {
            $this->eventDispatcher?->dispatch(
                new \App\Infrastructure\OpenAI\Event\ToolCall(
                    id: $toolCall->id,
                    name: $toolCall->name,
                    arguments: $toolCall->arguments,
                ),
            );
        }

        return match (true) {
            $finishReason === FinishReason::Stop => new StreamChatResponse(
                content: $result,
                finishReason: $finishReason->value,
            ),
            $finishReason === FinishReason::ToolCalls => new ToolCalledResponse(
                content: $result,
                tools: \array_values($toolCalls),
                finishReason: $finishReason->value,
            ),
        };
    }
}
