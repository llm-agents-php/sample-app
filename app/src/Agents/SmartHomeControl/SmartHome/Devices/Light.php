<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl\SmartHome\Devices;

final class Light extends SmartDevice
{
    public function __construct(
        string $id,
        string $name,
        string $room,
        public readonly string $type,
        protected int $brightness = 0,
        protected ?string $color = null,
    ) {
        parent::__construct($id, $name, $room);
    }

    public function setBrightness(int $level): void
    {
        $this->brightness = max(0, min(100, $level));
        $this->status = $this->brightness > 0;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    public function getDetails(): array
    {
        return [
            'type' => $this->type,
            'status' => $this->status ? 'on' : 'off',
            'brightness' => $this->brightness,
            'color' => $this->color,
        ];
    }
}
