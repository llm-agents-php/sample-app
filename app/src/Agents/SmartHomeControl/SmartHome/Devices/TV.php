<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl\SmartHome\Devices;

final class TV extends SmartDevice
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
}
