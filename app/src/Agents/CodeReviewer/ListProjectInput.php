<?php

declare(strict_types=1);

namespace App\Agents\CodeReviewer;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final class ListProjectInput
{
    public function __construct(
        #[Field(title: 'Project Name', description: 'The name of the project to list files from.')]
        public string $name,
    ) {}
}
