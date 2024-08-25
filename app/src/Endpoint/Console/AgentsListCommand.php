<?php

declare(strict_types=1);

namespace App\Endpoint\Console;

use LLM\Agents\Agent\AgentRegistryInterface;
use LLM\Agents\Solution\ToolLink;
use Spiral\Console\Attribute\AsCommand;
use Spiral\Console\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'agent:list',
    description: 'List all available agents.',
)]
final class AgentsListCommand extends Command
{
    public function __invoke(AgentRegistryInterface $agents): int
    {
        $io = new SymfonyStyle($this->input, $this->output);

        $io->block('Available agents:');

        $rows = [];
        foreach ($agents->all() as $agent) {
            $tools = \array_map(static fn(ToolLink $tool): string => '- ' . $tool->getName(), $agent->getTools());
            $rows[] = [
                $agent->getKey() . PHP_EOL . '- ' . $agent->getModel()->name,
                \implode(PHP_EOL, $tools),
                \wordwrap($agent->getDescription(), 50, "\n", true),
            ];
        }

        $io->table(['Agent', 'Tools', 'Description'], $rows);

        return self::SUCCESS;
    }
}
