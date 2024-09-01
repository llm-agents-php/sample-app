<?php

declare(strict_types=1);

namespace App\Endpoint\Console;

use LLM\Agents\Chat\ChatHistoryRepositoryInterface;
use LLM\Agents\Chat\ChatServiceInterface;
use LLM\Agents\Chat\Console\ChatHistory;
use Ramsey\Uuid\Uuid;
use Spiral\Console\Attribute\Argument;
use Spiral\Console\Attribute\AsCommand;
use Spiral\Console\Command;

#[AsCommand(
    name: 'chat:session',
    description: 'Chat session'
)]
final class ChatWindowCommand extends Command
{
    #[Argument(name: 'session_uuid')]
    public string $sessionUuid;

    public function __invoke(
        ChatHistoryRepositoryInterface $chatHistory,
        ChatServiceInterface $chatService,
    ): int {
        $chatWindow = new ChatHistory(
            input: $this->input,
            output: $this->output,
            chatHistory: $chatHistory,
            chat: $chatService,
        );

        $chatWindow->run(Uuid::fromString($this->sessionUuid));

        return self::SUCCESS;
    }
}
