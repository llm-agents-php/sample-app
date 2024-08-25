<?php

declare(strict_types=1);

namespace App\Agents\CodeReviewer;

use App\Domain\Tool\PhpTool;

/**
 * @extends PhpTool<ListProjectInput>
 */
final class ListProjectTool extends PhpTool
{
    public const NAME = 'list_project';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            inputSchema: ListProjectInput::class,
            description: 'List all files in a project with the given project name.',
        );
    }

    public function execute(object $input): string
    {
        // Implementation to list project files
        return json_encode(['files' => ['file1.php', 'file2.php']]);
    }
}
