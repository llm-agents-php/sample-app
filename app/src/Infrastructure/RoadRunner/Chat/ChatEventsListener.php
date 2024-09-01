<?php

declare(strict_types=1);

namespace App\Infrastructure\RoadRunner\Chat;

use LLM\Agents\Chat\ChatHistoryRepositoryInterface;
use LLM\Agents\Chat\Event\Message;
use LLM\Agents\Chat\Event\Question;
use LLM\Agents\Chat\Event\ToolCall;
use LLM\Agents\Chat\Event\ToolCallResult;
use Spiral\Events\Attribute\Listener;

final readonly class ChatEventsListener
{
    public function __construct(
        private ChatHistoryRepositoryInterface $history,
    ) {}

    #[Listener]
    public function listenToolCall(ToolCall $message): void
    {
        $this->history->addMessage($message->sessionUuid, $message);
    }

    #[Listener]
    public function listenMessage(Message $message): void
    {
        $this->history->addMessage($message->sessionUuid, $message);
    }

    #[Listener]
    public function listenQuestion(Question $message): void
    {
        $this->history->addMessage($message->sessionUuid, $message);
    }

    #[Listener]
    public function listenToolCallResult(ToolCallResult $message): void
    {
        $this->history->addMessage($message->sessionUuid, $message);
    }
}
