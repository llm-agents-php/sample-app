<?php

declare(strict_types=1);

namespace App\Endpoint\Console;

use Spiral\Boot\DirectoriesInterface;
use Spiral\Console\Attribute\AsCommand;
use Spiral\Console\Command;
use Spiral\Files\FilesInterface;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'kb:generate',
    description: 'Generate a code base example for agents and tools.',
)]
final class KnowledgeBaseGenerator extends Command
{
    private FilesInterface $files;
    private string $outputDir;
    private DirectoriesInterface $dirs;
    private string $basePath;

    public function __invoke(
        DirectoriesInterface $dirs,
        FilesInterface $files,
    ): int {
        $this->dirs = $dirs;
        $this->basePath = $dirs->get('root');
        $this->outputDir = $dirs->get('root') . '/knowledge-base';
        $this->files = $files;
        $files->ensureDirectory(directory: $this->outputDir);

        // Domain Layer
        $this->writeContent(
            description: <<<'TEXT'
This is an abstraction layer for the LLM Agents. It can be used as a template for creating new agents.
TEXT,
            sourcePath: [
                $dirs->get('root') . 'vendor/llm-agents/agents/src',
            ],
            outputPath: $this->outputDir . '/domain-layer.txt',
        );

        // Packages
        $this->writeContent(
            description: <<<'TEXT'
This is an abstraction layer for the LLM Agents. It can be used as a template for creating new agents.
TEXT,
            sourcePath: [
                $dirs->get('root') . 'vendor/llm-agents/prompt-generator/src',
                $dirs->get('root') . 'vendor/llm-agents/openai-client/src',
                $dirs->get('root') . 'vendor/llm-agents/json-schema-mapper/src',
            ],
            outputPath: $this->outputDir . '/packages-layer.txt',
        );

        // Cli-chat
        $this->writeContent(
            description: <<<'TEXT'
This is an abstraction layer for the LLM Agents. It can be used as a template for creating new agents.
TEXT,
            sourcePath: [
                $dirs->get('root') . 'vendor/llm-agents/cli-chat/src',
            ],
            outputPath: $this->outputDir . '/cli-chat.txt',
        );

        // Agents Example
        $this->writeContent(
            description: <<<'TEXT'
This is an example of Agent with Tools, Memory and Initial Prompts. It can be used as a template for creating new agents and tools.
Important: Always use gp4-0-mini as the base model for new agents.
TEXT,
            sourcePath: [
                $dirs->get('app') . 'src/Agents',
                $dirs->get('root') . 'vendor/llm-agents/agent-site-status-checker/src',
                $dirs->get('root') . 'vendor/llm-agents/agent-symfony-console/src',
            ],
            outputPath: $this->outputDir . '/agents-example.txt',
        );

        // Application example
        $this->writeContent(
            description: <<<'TEXT'
This is an example of Application Layer. It can be used as a template for creating new applications.
TEXT,
            sourcePath: [
                $dirs->get('app') . 'src/Application',
                $dirs->get('app') . 'src/Domain',
                $dirs->get('app') . 'src/Infrastructure',
            ],
            outputPath: $this->outputDir . '/app-example.txt',
        );

        return self::SUCCESS;
    }

    private function writeContent(
        string $description,
        string|array $sourcePath,
        string $outputPath,
    ): void {
        $found = Finder::create()->name('*.php')->in($sourcePath);

        $description .= PHP_EOL;

        foreach ($found as $file) {
            $description .= '//' . \trim(\str_replace($this->basePath, '', $file->getPath())) . PHP_EOL;
            $description .= \str_replace(['<?php', 'declare(strict_types=1);'], '', $file->getContents()) . PHP_EOL;
        }

        $description = \preg_replace('/^\s*[\r\n]+/m', '', $description);

        $this->info('Writing ' . $outputPath);
        $this->files->write($outputPath, $description);
    }
}
