<?php

declare(strict_types=1);

namespace App\Agents\Delivery;

use App\Domain\Tool\PhpTool;
use Carbon\Carbon;

/**
 * @extends  PhpTool<DeliveryDateInput>
 */
final class GetDeliveryDateTool extends PhpTool
{
    public const NAME = 'get_delivery_date';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            inputSchema: DeliveryDateInput::class,
            description: 'Get the delivery date for a customer\'s order. Call this whenever you need to know the delivery date, for example when a customer asks \'Where is my package\'',
        );
    }

    public function execute(object $input): string
    {
        return \json_encode([
            'delivery_date' => Carbon::now()->addDays(\rand(1, 100))->toDateString(),
        ]);
    }
}
