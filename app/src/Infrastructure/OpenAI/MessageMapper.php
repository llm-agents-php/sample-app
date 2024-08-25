<?php

declare(strict_types=1);

namespace App\Infrastructure\OpenAI;

use LLM\Agents\LLM\Prompt\Chat\ChatMessage;
use LLM\Agents\LLM\Prompt\Chat\Role;
use LLM\Agents\LLM\Prompt\Chat\ToolCalledPrompt;
use LLM\Agents\LLM\Prompt\Chat\ToolCallResultMessage;
use LLM\Agents\LLM\Prompt\Tool;
use LLM\Agents\LLM\Response\ToolCall;

final readonly class MessageMapper
{
    public function map(object $message): array
    {
        if ($message instanceof ChatMessage) {
            return [
                'content' => $message->content,
                'role' => $message->role->value,
            ];
        }

        if ($message instanceof ToolCallResultMessage) {
            return [
                'content' => \is_array($message->content) ? \json_encode($message->content) : $message->content,
                'tool_call_id' => $message->id,
                'role' => $message->role->value,
            ];
        }

        if ($message instanceof ToolCalledPrompt) {
            return [
                'content' => null,
                'role' => Role::Assistant->value,
                'tool_calls' => \array_map(
                    static fn(ToolCall $tool): array => [
                        'id' => $tool->id,
                        'type' => 'function',
                        'function' => [
                            'name' => $tool->name,
                            'arguments' => $tool->arguments,
                        ],
                    ],
                    $message->tools,
                ),
            ];
        }

        if ($message instanceof Tool) {
            return [
                'type' => 'function',
                'function' => [
                    'name' => $message->name,
                    'description' => $message->description,
                    'parameters' => [
                            'type' => 'object',
                            'additionalProperties' => $message->additionalProperties,
                        ] + $message->parameters,
                    'strict' => $message->strict,
                ],
            ];
        }

        if ($message instanceof \JsonSerializable) {
            return $message->jsonSerialize();
        }

        throw new \InvalidArgumentException('Invalid message type');
    }
}
