<?php

declare(strict_types=1);

namespace App\Agents\Delivery;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final readonly class ProfileInput
{
    public function __construct(
        #[Field(title: 'Account ID', description: 'The customer\'s account ID.')]
        public string $accountId,
    ) {}
}
