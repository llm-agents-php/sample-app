<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl\SmartHome\Devices;

use App\Agents\SmartHomeControl\DeviceAction;

class TV extends SmartDevice
{
    public function __construct(
        string $id,
        string $name,
        string $room,
        protected int $volume = 0,
        protected string $input = 'HDMI 1',
    ) {
        parent::__construct($id, $name, $room);
    }

    public function setVolume(int $volume): void
    {
        $this->volume = max(0, min(100, $volume));
    }

    public function setInput(string $input): void
    {
        $this->input = $input;
    }

    public function getDetails(): array
    {
        return [
            'status' => $this->status ? 'on' : 'off',
            'volume' => $this->volume,
            'input' => $this->input,
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
                        DeviceAction::SetVolume->value,
                        DeviceAction::SetInput->value,
                    ],
                ],
                'volume' => [
                    'type' => 'integer',
                    'minimum' => 0,
                    'maximum' => 100,
                ],
                'input' => [
                    'type' => 'string',
                    'enum' => ['HDMI 1', 'HDMI 2', 'HDMI 3', 'TV', 'AV'],
                ],
            ],
            'required' => ['action'],
        ];
    }

    public function executeAction(DeviceAction $action, array $params): static
    {
        match ($action) {
            DeviceAction::SetVolume => $this->setVolume($params['volume']),
            DeviceAction::SetInput => $this->setInput($params['input']),
            default => parent::executeAction($action, $params),
        };

        return $this;
    }
}
