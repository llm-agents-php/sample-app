<?php

declare(strict_types=1);

namespace App\Endpoint\Console;

use App\Application\Entity\Uuid;
use App\Domain\Chat\ChatServiceInterface;
use LLM\Agents\Agent\AgentInterface;
use LLM\Agents\Agent\AgentRegistryInterface;
use LLM\Agents\Tool\ToolRegistryInterface;
use Spiral\Cache\CacheStorageProviderInterface;
use Spiral\Console\Attribute\AsCommand;
use Spiral\Console\Command;
use Spiral\Console\Console;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'chat',
    description: 'Chat session'
)]
final class ChatCommand extends Command
{
    private Uuid $sessionUuid;
    private SymfonyStyle $io;

    public function __invoke(
        AgentRegistryInterface $agents,
        ChatServiceInterface $chat,
        Console $console,
        CacheStorageProviderInterface $cache,
        ToolRegistryInterface $tools,
    ): int {
        $cache = $cache->storage('chat-messages');
        $console->run(command: 'agent:list', output: $this->output);
        $this->io = new SymfonyStyle($this->input, $this->output);

        $availableAgents = [];

        foreach ($agents->all() as $agent) {
            $availableAgents[$agent->getKey()] = $agent->getName();
        }

        while (true) {
            $agentName = $this->choiceQuestion(
                'Hello! Let\'s start a chat session. Please select an agent:',
                $availableAgents,
            );

            if ($agentName && $agents->has($agentName)) {
                $agent = $agents->get($agentName);
                $this->io->title($agent->getName());

                // split the description into multiple lines by 200 characters
                $this->io->block(\wordwrap($agent->getDescription(), 200, "\n", true));

                $rows = [];
                foreach ($agent->getTools() as $tool) {
                    $tool = $tools->get($tool->name);
                    $rows[] = [$tool->name, \wordwrap($tool->description, 70, "\n", true)];
                }
                $this->io->table(['Tool', 'Description'], $rows);

                break;
            }

            $this->error('Invalid agent');
        }

        $getCommand = $this->getCommand($agent);

        $accountUuid = Uuid::generate();
        $this->sessionUuid = $chat->startSession(
            accountUuid: $accountUuid,
            agentName: $agentName,
        );

        if ($this->isVerbose()) {
            $this->info(\sprintf('Session started with UUID: %s', $this->sessionUuid));
        }

        $this->alert('Run the following command to see the AI response');
        $this->warning(\sprintf('php app.php chat:session %s -v', $this->sessionUuid));

        while (true) {
            $message = $getCommand();

            if ($message === 'exit') {
                $this->info('Goodbye! Closing chat session...');
                $chat->closeSession($this->sessionUuid);

                $cache->delete((string) $this->sessionUuid);

                break;
            } elseif ($message === 'refresh') {
                continue;
            }

            if (!empty($message)) {
                $chat->ask($this->sessionUuid, $message);
            } else {
                $this->warning('Message cannot be empty');
            }
        }

        return self::SUCCESS;
    }

    private function getCommand(AgentInterface $agent): callable
    {
        return function () use ($agent): string|null {
            $initialPrompts = ['custom'];

            foreach ($agent->getPrompts() as $prompt) {
                $initialPrompts[] = $prompt->content;
            }

            $initialPrompts[] = 'reset';
            $initialPrompts[] = 'exit';

            $initialPrompt = $this->choiceQuestion('Choose a prompt:', $initialPrompts, 'custom');
            if ($initialPrompt === 'custom') {
                // Re-enable input echoing in case it was disabled
                \shell_exec('stty sane');
                $initialPrompt = $this->ask('You');
            }

            return $initialPrompt;
        };
    }
}
