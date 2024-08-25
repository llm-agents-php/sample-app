<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl;


use Spiral\JsonSchemaGenerator\Attribute\Field;

final readonly class ControlDeviceInput
{
    public function __construct(
        #[Field(title: 'Device ID', description: 'The unique identifier of the device to control')]
        public string $deviceId,

        #[Field(title: 'Action', description: 'The action to perform on the device (e.g., turnOn, turnOff, setBrightness)')]
        public string $action,

        /**
         * @var array<DeviceParam>
         */
        #[Field(title: 'Parameters', description: 'Additional parameters for the action. If the action does not require parameters, this field should be an empty array')]
        public array $params,
    ) {}
}
