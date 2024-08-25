<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final readonly class ListRoomDevicesInput
{
    public function __construct(
        #[Field(title: 'Room Name', description: 'The name of the room to list devices from')]
        public string $roomName,
    ) {}
}
