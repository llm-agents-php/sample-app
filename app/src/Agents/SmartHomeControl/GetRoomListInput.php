<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final readonly class GetRoomListInput
{
    public function __construct(
        // The input parameter is the name of the room to list devices from
        #[Field(title: 'Home Name', description: 'The name of the home to list rooms from')]
        public string $home,
    ) {
    }
}
