<?php

declare(strict_types=1);

namespace App\Agents\Delivery;

use LLM\Agents\Tool\PhpTool;

/**
 * @extends PhpTool<OrderNumberInput>
 */
final class GetOrderNumberTool extends PhpTool
{
    public const NAME = 'get_order_number';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            inputSchema: OrderNumberInput::class,
            description: 'Get the order number for a customer.',
        );
    }

    public function execute(object $input): string
    {
        return \json_encode([
            'customer_id' => $input->customerId,
            'customer' => 'John Doe',
            'order_number' => 'abc-' . $input->customerId,
        ]);
    }
}
