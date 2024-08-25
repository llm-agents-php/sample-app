<?php

declare(strict_types=1);

namespace App\Endpoint\Console;

use LLM\Agents\Tool\ToolRegistryInterface;
use Spiral\Console\Attribute\AsCommand;
use Spiral\Console\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tool:list',
    description: 'List all available tools.',
)]
final class ToolListCommand extends Command
{
    public function __invoke(ToolRegistryInterface $tools): int
    {
        $io = new SymfonyStyle($this->input, $this->output);

        $io->block('Available tools:');

        $rows = [];
        foreach ($tools->all() as $tool) {
            $rows[] = [$tool->getName(), $tool->getDescription()];
        }

        $io->table(['Tool', 'Description'], $rows);

        return self::SUCCESS;
    }
}
