<?php

declare(strict_types=1);

namespace App\Domain\Chat;

use App\Application\Entity\Uuid;
use App\Domain\Agent\AgentExecutorBuilder;
use App\Domain\Chat\Exception\ChatNotFoundException;
use App\Infrastructure\OpenAI\StreamChunkCallback;
use LLM\Agents\Agent\AgentRepositoryInterface;
use LLM\Agents\Agent\Exception\AgentNotFoundException;
use LLM\Agents\Agent\Execution;
use LLM\Agents\LLM\Prompt\Chat\Prompt;
use LLM\Agents\LLM\Prompt\Chat\ToolCallResultMessage;
use LLM\Agents\LLM\Response\ChatResponse;
use LLM\Agents\LLM\Response\ToolCall;
use LLM\Agents\LLM\Response\ToolCalledResponse;
use LLM\Agents\Tool\ToolExecutor;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class SimpleChatService implements ChatServiceInterface
{
    public function __construct(
        private AgentExecutorBuilder $builder,
        private SessionRepositoryInterface $sessions,
        private EntityManagerInterface $em,
        private AgentRepositoryInterface $agents,
        private ToolExecutor $toolExecutor,
        private ?EventDispatcherInterface $eventDispatcher = null,
    ) {}

    public function getSession(Uuid $sessionUuid): Session
    {
        $session = $this->sessions->forUpdate()->getByUuid($sessionUuid);

        if ($session->isFinished()) {
            throw new ChatNotFoundException('Session is finished');
        }

        return $session;
    }

    public function startSession(Uuid $accountUuid, string $agentName): Uuid
    {
        if (!$this->agents->has($agentName)) {
            throw new AgentNotFoundException($agentName);
        }

        $agent = $this->agents->get($agentName);

        $session = new Session(
            uuid: Uuid::generate(),
            accountUuid: $accountUuid,
            agentName: $agentName,
        );

        // Set the title of the session to the agent's description.
        $session->title = $agent->getDescription();

        $this->updateSession($session);

        return $session->uuid;
    }

    public function ask(Uuid $sessionUuid, string|\Stringable $message): Uuid
    {
        $session = $this->getSession($sessionUuid);

        $prompt = null;
        if (!$session->history->isEmpty()) {
            $prompt = $session->history->toPrompt();
        }

        $messageUuid = Uuid::generate();

        $this->eventDispatcher->dispatch(
            new Event\Question(
                sessionUuid: $session->uuid,
                messageUuid: $messageUuid,
                createdAt: new \DateTimeImmutable(),
                message: $message,
            ),
        );

        $execution = $this->buildAgent(
            session: $session,
            prompt: $prompt,
        )->ask($message);

        $this->handleResult($execution, $session);

        return $messageUuid;
    }

    public function closeSession(Uuid $sessionUuid): void
    {
        $session = $this->getSession($sessionUuid);
        $session->finishedAt = new \DateTimeImmutable();

        $this->updateSession($session);
    }

    public function updateSession(Session $session): void
    {
        $this->em->persist($session)->flush();
    }

    private function handleResult(Execution $execution, Session $session): void
    {
        $finished = false;
        while (true) {
            $result = $execution->result;
            $prompt = $execution->prompt;

            if ($result instanceof ToolCalledResponse) {
                // First, call all tools.
                $toolsResponse = [];
                foreach ($result->tools as $tool) {
                    $toolsResponse[] = $this->callTool($session, $tool);
                }

                // Then add the tools responses to the prompt.
                foreach ($toolsResponse as $toolResponse) {
                    $prompt = $prompt->withAddedMessage($toolResponse);
                }

                $execution = $this->buildAgent(
                    session: $session,
                    prompt: $prompt,
                )->continue();
            } elseif ($result instanceof ChatResponse) {
                $finished = true;

                $this->eventDispatcher->dispatch(
                    new Event\Message(
                        sessionUuid: $session->uuid,
                        createdAt: new \DateTimeImmutable(),
                        message: $result->content,
                    ),
                );
            }

            $session->updateHistory($prompt->toArray());
            $this->updateSession($session);

            if ($finished) {
                break;
            }
        }
    }

    private function buildAgent(Session $session, ?Prompt $prompt): AgentExecutorBuilder
    {
        $agent = $this->builder
            ->withAgentKey($session->agentName)
            ->withStreamChunkCallback(
                new StreamChunkCallback(
                    sessionUuid: $session->uuid,
                    eventDispatcher: $this->eventDispatcher,
                ),
            )
            ->withSessionContext([
                'account_uuid' => (string) $session->accountUuid,
                'session_uuid' => (string) $session->uuid,
            ]);

        if ($prompt !== null) {
            $agent = $agent->withPrompt($prompt);
        }

        return $agent;
    }

    private function callTool(Session $session, ToolCall $tool): ToolCallResultMessage
    {
        $this->eventDispatcher->dispatch(
            new Event\ToolCall(
                sessionUuid: $session->uuid,
                id: $tool->id,
                tool: $tool->name,
                arguments: $tool->arguments,
                createdAt: new \DateTimeImmutable(),
            ),
        );

        $functionResult = $this->toolExecutor->execute($tool->name, $tool->arguments);

        $this->eventDispatcher->dispatch(
            new Event\ToolCallResult(
                sessionUuid: $session->uuid,
                id: $tool->id,
                tool: $tool->name,
                result: $functionResult,
                createdAt: new \DateTimeImmutable(),
            ),
        );

        return new ToolCallResultMessage(
            id: $tool->id,
            content: [$functionResult],
        );
    }
}
