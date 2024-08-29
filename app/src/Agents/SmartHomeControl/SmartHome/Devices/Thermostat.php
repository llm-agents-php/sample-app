<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl\SmartHome\Devices;

use App\Agents\SmartHomeControl\DeviceAction;

class Thermostat extends SmartDevice
{
    public function __construct(
        string $id,
        string $name,
        string $room,
        protected int $temperature = 72,
        protected string $mode = 'auto',
    ) {
        parent::__construct($id, $name, $room, true);
    }

    public function setTemperature(int $temperature): void
    {
        $this->temperature = $temperature;
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    public function getDetails(): array
    {
        return [
            'status' => $this->status ? 'on' : 'off',
            'temperature' => $this->temperature,
            'mode' => $this->mode,
        ];
    }

    public function getControlSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'action' => [
                    'type' => 'string',
                    'enum' => [
                        DeviceAction::TurnOn->value,
                        DeviceAction::TurnOff->value,
                        DeviceAction::SetTemperature->value,
                        DeviceAction::SetMode->value,
                    ],
                ],
                'temperature' => [
                    'type' => 'integer',
                    'minimum' => 60,
                    'maximum' => 90,
                ],
                'mode' => [
                    'type' => 'string',
                    'enum' => ['auto', 'cool', 'heat', 'fan'],
                ],
            ],
            'required' => ['action'],
        ];
    }

    public function executeAction(DeviceAction $action, array $params): static
    {
        match ($action) {
            DeviceAction::SetTemperature => $this->setTemperature($params['temperature']),
            DeviceAction::SetMode => $this->setMode($params['mode']),
            default => parent::executeAction($action, $params),
        };

        return $this;
    }
}
