<?php

declare(strict_types=1);

namespace App\Domain\Chat;

use App\Agents\DynamicMemoryTool\DynamicMemoryService;
use App\Application\Entity\Uuid;
use LLM\Agents\Agent\AgentRepositoryInterface;
use LLM\Agents\Agent\Exception\AgentNotFoundException;
use LLM\Agents\Agent\Execution;
use LLM\Agents\Chat\AgentExecutorBuilder;
use LLM\Agents\Chat\ChatServiceInterface;
use LLM\Agents\Chat\Exception\ChatNotFoundException;
use LLM\Agents\Chat\SessionInterface;
use LLM\Agents\Chat\StreamChunkCallback;
use LLM\Agents\LLM\Prompt\Chat\Prompt;
use LLM\Agents\LLM\Prompt\Chat\ToolCallResultMessage;
use LLM\Agents\LLM\Response\ChatResponse;
use LLM\Agents\LLM\Response\ToolCall;
use LLM\Agents\LLM\Response\ToolCalledResponse;
use LLM\Agents\Solution\SolutionMetadata;
use LLM\Agents\Tool\ToolExecutor;
use Psr\EventDispatcher\EventDispatcherInterface;
use Ramsey\Uuid\UuidInterface;

final readonly class SimpleChatService implements ChatServiceInterface
{
    public function __construct(
        private AgentExecutorBuilder $builder,
        private SessionRepositoryInterface $sessions,
        private EntityManagerInterface $em,
        private AgentRepositoryInterface $agents,
        private ToolExecutor $toolExecutor,
        private DynamicMemoryService $memoryService,
        private ?EventDispatcherInterface $eventDispatcher = null,
    ) {}

    public function getSession(UuidInterface $sessionUuid): SessionInterface
    {
        $session = $this->sessions->forUpdate()->getByUuid($sessionUuid);

        if ($session->isFinished()) {
            throw new ChatNotFoundException('Session is finished');
        }

        return $session;
    }

    public function startSession(UuidInterface $accountUuid, string $agentName): UuidInterface
    {
        if (!$this->agents->has($agentName)) {
            throw new AgentNotFoundException($agentName);
        }

        $agent = $this->agents->get($agentName);

        $session = new Session(
            uuid: Uuid::generate(),
            accountUuid: new Uuid($accountUuid),
            agentName: $agentName,
        );

        // Set the title of the session to the agent's description.
        $session->title = $agent->getDescription();

        $this->updateSession($session);

        return $session->uuid->uuid;
    }

    public function ask(UuidInterface $sessionUuid, string|\Stringable $message): UuidInterface
    {
        $session = $this->getSession($sessionUuid);

        $prompt = null;
        if (!$session->history->isEmpty()) {
            $prompt = $session->history->toPrompt();
        }

        $messageUuid = Uuid::generate();

        $this->eventDispatcher->dispatch(
            new \LLM\Agents\Chat\Event\Question(
                sessionUuid: $session->uuid->uuid,
                messageUuid: $messageUuid->uuid,
                createdAt: new \DateTimeImmutable(),
                message: $message,
            ),
        );

        $execution = $this->buildAgent(
            session: $session,
            prompt: $prompt,
        )->ask($message);

        $this->handleResult($execution, $session);

        return $messageUuid->uuid;
    }

    public function closeSession(UuidInterface $sessionUuid): void
    {
        $session = $this->getSession($sessionUuid);
        $session->finishedAt = new \DateTimeImmutable();

        $this->updateSession($session);
    }

    public function updateSession(SessionInterface $session): void
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
                    new \LLM\Agents\Chat\Event\Message(
                        sessionUuid: $session->uuid->uuid,
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
                    sessionUuid: $session->uuid->uuid,
                    eventDispatcher: $this->eventDispatcher,
                ),
            )
            ->withSessionContext([
                'account_uuid' => (string) $session->accountUuid,
                'session_uuid' => (string) $session->uuid,
            ]);

        if ($prompt === null) {
            return $agent;
        }

        $memories = $this->memoryService->getCurrentMemory($session->uuid);


        return $agent->withPrompt($prompt->withValues([
            'dynamic_memory' => \implode(
                "\n",
                \array_map(
                    fn(SolutionMetadata $memory) => $memory->content,
                    $memories->memories,
                ),
            ),
        ]));
    }

    private function callTool(Session $session, ToolCall $tool): ToolCallResultMessage
    {
        $this->eventDispatcher->dispatch(
            new \LLM\Agents\Chat\Event\ToolCall(
                sessionUuid: $session->uuid->uuid,
                id: $tool->id,
                tool: $tool->name,
                arguments: $tool->arguments,
                createdAt: new \DateTimeImmutable(),
            ),
        );

        $functionResult = $this->toolExecutor->execute($tool->name, $tool->arguments);

        $this->eventDispatcher->dispatch(
            new \LLM\Agents\Chat\Event\ToolCallResult(
                sessionUuid: $session->uuid->uuid,
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
