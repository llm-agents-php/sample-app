<?php

declare(strict_types=1);

namespace App\Agents\CodeReviewer;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final class ReadFileInput
{
    public function __construct(
        #[Field(title: 'File Path', description: 'The path of the file to read.')]
        public string $path,
    ) {}
}
