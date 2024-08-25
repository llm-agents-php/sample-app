<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl\SmartHome\Devices;

final class Thermostat extends SmartDevice
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
}
