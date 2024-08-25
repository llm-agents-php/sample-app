<?php

declare(strict_types=1);

namespace App\Agents\SiteStatusChecker;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final readonly class PerformPingTestInput
{
    public function __construct(
        #[Field(title: 'Host', description: 'The hostname or IP address to ping')]
        public string $host,
    ) {}
}
