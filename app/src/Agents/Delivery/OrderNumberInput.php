<?php

declare(strict_types=1);

namespace App\Agents\Delivery;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final readonly class OrderNumberInput
{
    public function __construct(
        #[Field(title: 'Customer ID', description: 'The Customer ID')]
        public string $customerId,
    ) {}
}
