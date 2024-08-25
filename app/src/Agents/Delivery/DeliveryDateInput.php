<?php

declare(strict_types=1);

namespace App\Agents\Delivery;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final readonly class DeliveryDateInput
{
    public function __construct(
        #[Field(title: 'Order ID', description: 'The customer\'s order ID. Should be always uppercase!')]
        public string $orderId,
    ) {}
}
