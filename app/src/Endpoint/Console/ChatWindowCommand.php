<?php

declare(strict_types=1);

namespace App\Endpoint\Console;

use App\Application\Entity\Uuid;
use App\Domain\Chat\ChatHistoryRepositoryInterface;
use App\Domain\Chat\ChatServiceInterface;
use App\Domain\Chat\Event\Message;
use App\Domain\Chat\Event\MessageChunk;
use App\Domain\Chat\Event\Question;
use App\Domain\Chat\Event\ToolCall;
use App\Domain\Chat\Event\ToolCallResult;
use App\Domain\Chat\Exception\ChatNotFoundException;
use Spiral\Console\Attribute\Argument;
use Spiral\Console\Attribute\AsCommand;
use Spiral\Console\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'chat:session',
    description: 'Chat session'
)]
final class ChatWindowCommand extends Command
{
    #[Argument(name: 'session_uuid')]
    public string $sessionUuid;

    private array $shownMessages = [];
    private SymfonyStyle $io;

    private bool $shouldStop = false;

    public function __invoke(
        ChatHistoryRepositoryInterface $chatHistory,
        ChatServiceInterface $chat,
    ): int {
        $this->clearScreen();

        $this->info(\sprintf('Connecting to chat session [%s]...', $this->sessionUuid));
        $session = $chat->getSession(Uuid::fromString($this->sessionUuid));
        $this->io = new SymfonyStyle($this->input, $this->output);

        $this->info(
            \sprintf(
                'Chat session started with agent [%s]. Press Ctrl+C to exit.',
                $session->agentName,
            ),
        );

        do {
            try {
                $chat->getSession(Uuid::fromString($this->sessionUuid));
            } catch (ChatNotFoundException) {
                $this->error(\sprintf('Session is closed.'));
                return self::FAILURE;
            }

            foreach ($this->iterateMessages($chatHistory) as $message) {
                if ($message instanceof MessageChunk) {
                    $this->write($message->message);
                    \usleep(20_000);
                    if ($message->isLast) {
                        $this->newLine();
                    }
                } elseif ($message instanceof Message) {
//                    $this->write($message->message);
//                    $this->newLine();
                } elseif ($message instanceof Question) {
                    $this->newLine();
                    $this->io->block(\sprintf('> User: %s', $message->message), style: 'info');
                } elseif ($message instanceof ToolCall) {
                    $this->io->block(
                        \sprintf(
                            "<-- Let me call [%s] tool",
                            $message->tool,
                        ),
                        style: 'info',
                    );

                    if ($this->isVerbose()) {
                        $this->io->block(
                            messages: \json_encode(\json_decode($message->arguments, true), \JSON_PRETTY_PRINT),
                            style: 'comment',
                        );
                    }
                } elseif ($message instanceof ToolCallResult) {
                    $this->io->block(
                        \sprintf(
                            "--> [%s]",
                            $message->tool,
                        ),
                        style: 'info',
                    );

                    if ($this->isVerbose()) {
                        // unescape the JSON string
                        $result = \str_replace('\\"', '"', $message->result);

                        $this->io->block(
                            messages: \json_validate($result)
                                ? \json_encode(\json_decode($result, true), \JSON_PRETTY_PRINT)
                                : $result,
                            style: 'info',
                        );
                    }
                }
            }

            \sleep(2);
        } while (!$this->shouldStop);

        return self::SUCCESS;
    }

    private function clearScreen()
    {
        // Clear the console screen
        $this->output->write("\033\143");
    }

    /**
     * @return iterable<Message|ToolCall|ToolCallResult|MessageChunk>
     */
    private function iterateMessages(ChatHistoryRepositoryInterface $chatHistory): iterable
    {
        $messages = $chatHistory->getMessages(Uuid::fromString($this->sessionUuid));

        foreach ($messages as $message) {
            if ($message instanceof Message || $message instanceof Question || $message instanceof MessageChunk) {
                if (\in_array((string) $message->uuid, $this->shownMessages, true)) {
                    continue;
                }

                $this->shownMessages[] = (string) $message->uuid;
                yield $message;
            } elseif ($message instanceof ToolCall) {
                if (\in_array($message->id . 'ToolCall', $this->shownMessages, true)) {
                    continue;
                }

                $this->shownMessages[] = $message->id . 'ToolCall';
                yield $message;
            } elseif ($message instanceof ToolCallResult) {
                if (\in_array($message->id . 'ToolCallResult', $this->shownMessages, true)) {
                    continue;
                }

                $this->shownMessages[] = $message->id . 'ToolCallResult';
                yield $message;
            }
        }
    }
}
