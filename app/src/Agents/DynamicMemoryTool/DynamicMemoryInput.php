<?php

declare(strict_types=1);

namespace App\Agents\DynamicMemoryTool;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final class DynamicMemoryInput
{
    public function __construct(
        #[Field(title: 'Session ID', description: 'The unique identifier for the current session')]
        public string $sessionId,
        #[Field(title: 'User preference', description: 'The user preference to add or update')]
        public string $preference,
    ) {}
}
