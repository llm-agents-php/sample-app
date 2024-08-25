<?php

declare(strict_types=1);

namespace App\Agents\Delivery;

use App\Domain\Tool\PhpTool;

/**
 * @extends  PhpTool<ProfileInput>
 */
final class GetProfileTool extends PhpTool
{
    public const NAME = 'get_profile';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            inputSchema: ProfileInput::class,
            description: 'Get the customer\'s profile information.',
        );
    }

    public function execute(object $input): string
    {
        return \json_encode([
            'account_uuid' => $input->accountId,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'age' => \rand(10, 100),
        ]);
    }
}
