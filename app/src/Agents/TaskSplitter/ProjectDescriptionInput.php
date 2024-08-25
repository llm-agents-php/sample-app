<?php

declare(strict_types=1);

namespace App\Agents\TaskSplitter;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final class ProjectDescriptionInput
{
    public function __construct(
        #[Field(title: 'Project ID', description: 'The ID of the project to get the description for.')]
        public string $uuid,
    ) {}
}
