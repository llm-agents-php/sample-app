<?php

declare(strict_types=1);

namespace App\Agents\CodeReviewer;

use LLM\Agents\Tool\PhpTool;

/**
 * @extends PhpTool<ReadFileInput>
 */
final class ReadFileTool extends PhpTool
{
    public const NAME = 'read_file';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            inputSchema: ReadFileInput::class,
            description: 'Read the contents of a file at the given path.',
        );
    }

    public function execute(object $input): string
    {
        if ($input->path === 'file1.php') {
            return json_encode([
                'content' => <<<'PHP'
class ReviewTool extends \App\Domain\Tool\Tool
{
    public function __construct()
    {
        parent::__construct(
            name: 'review'
            inputSchema: ReviewInput::class,
            description: 'Submit a code review for a file at the given path.',
        );
    }

    public function getLanguage(): \App\Domain\Tool\ToolLanguage
    {
        return \App\Domain\Tool\ToolLanguage::PHP;
    }

    public function execute(object $input): string
    {
        // Implementation to submit code review
        return json_encode(['status' => 'success', 'message' => 'Code review submitted']);
    }
}
PHP,
            ]);
        }

        if ($input->path === 'file2.php') {
            return json_encode([
                'content' => <<<'PHP'
class ReadFileTool extends \App\Domain\Tool\Tool
{
    public function __construct()
    {
        parent::__construct(
            name: 'read_file',
            inputSchema: ReadFileInput::class,
            description: 'Read the contents of a file at the given path.',
        );
    }

    public function getLanguage(): \App\Domain\Tool\ToolLanguage
    {
        return \App\Domain\Tool\ToolLanguage:PHP;
    }

    public function execute(object $input): string
    {
        // Implementation to read file contents
        return json_encode(['content' => 'File contents here']);
    }
}
PHP,
            ]);
        }

        return 'File not found';
    }
}
