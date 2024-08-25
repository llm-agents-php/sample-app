<?php

declare(strict_types=1);

namespace App\Agents\CodeReviewer;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final class ReviewInput
{
    public function __construct(
        #[Field(title: 'Project Name', description: 'The name of the project.')]
        public string $name,
        #[Field(title: 'File Path', description: 'The path of the file being reviewed.')]
        public string $path,
        #[Field(title: 'Review Result', description: 'Code diff with suggested changes.')]
        public string $result,
    ) {}
}
