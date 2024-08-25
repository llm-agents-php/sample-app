<?php

declare(strict_types=1);

namespace App\Agents\TaskSplitter;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final readonly class TaskCreateInput
{
    public function __construct(
        #[Field(title: 'Project UUID', description: 'The UUID of the project to which the task belongs.')]
        public string $projectUuid,

        #[Field(title: 'Task Name', description: 'The name of the task to be created.')]
        public string $name,

        #[Field(title: 'Task Description', description: 'The description of the task to be created.')]
        public string $description,

        #[Field(title: 'Parent Task ID', description: 'The ID of the parent task if this is a subtask. Empty string for top-level tasks. Multiple levels should be separated by a "/".')]
        public string $parentTaskUuid,
    ) {}
}
