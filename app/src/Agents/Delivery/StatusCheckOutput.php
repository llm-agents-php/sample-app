<?php

declare(strict_types=1);

namespace App\Agents\Delivery;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final class StatusCheckOutput
{
    public function __construct(
        #[Field(title: 'Status Code', description: 'The server status code.')]
        public string $code,
        #[Field(title: 'Response Time', description: 'The server response time in milliseconds.')]
        public string $responseTime,
        #[Field(title: 'Url', description: 'The server URL.')]
        public string $url,
    ) {}
}
