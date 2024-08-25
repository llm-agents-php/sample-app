<?php

declare(strict_types=1);

namespace App\Infrastructure\RoadRunner\Chat;

use App\Domain\Chat\ChatHistoryRepositoryInterface;
use App\Domain\Chat\Event\Message;
use App\Domain\Chat\Event\Question;
use App\Domain\Chat\Event\ToolCall;
use App\Domain\Chat\Event\ToolCallResult;
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
