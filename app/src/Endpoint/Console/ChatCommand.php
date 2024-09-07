<?php

declare(strict_types=1);

namespace App\Endpoint\Console;

use LLM\Agents\Agent\AgentRegistryInterface;
use LLM\Agents\Chat\ChatHistoryRepositoryInterface;
use LLM\Agents\Chat\ChatServiceInterface;
use LLM\Agents\Chat\Console\ChatSession;
use LLM\Agents\Tool\ToolRegistryInterface;
use Ramsey\Uuid\Uuid;
use Spiral\Console\Attribute\AsCommand;
use Spiral\Console\Attribute\Option;
use Spiral\Console\Command;
use Spiral\Console\Console;
use Symfony\Component\Console\Cursor;

#[AsCommand(
    name: 'chat',
    description: 'Chat session'
)]
final class ChatCommand extends Command
{
    #[Option(name: 'latest', shortcut: 'l', description: 'Open latest chat session')]
    public bool $openLatest = false;

    public function __invoke(
        AgentRegistryInterface $agents,
        ChatServiceInterface $chat,
        Console $console,
        ChatHistoryRepositoryInterface $chatHistory,
        ToolRegistryInterface $tools,
    ): int {
        $cursor = new Cursor($this->output);
        $cursor->clearScreen();
        $console->run(command: 'agent:list', output: $this->output);

        $chat = new ChatSession(
            input: $this->input,
            output: $this->output,
            agents: $agents,
            chat: $chat,
            chatHistory: $chatHistory,
            tools: $tools,
        );

        $chat->run(
            accountUuid: Uuid::fromString('00000000-0000-0000-0000-000000000000'),
            openLatest: $this->openLatest,
        );

        return self::SUCCESS;
    }
}
