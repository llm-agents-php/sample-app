<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl\SmartHome\Devices;

abstract class SmartDevice
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $room,
        protected bool $status = false,
    ) {}

    public function turnOn(): void
    {
        $this->status = true;
    }

    public function turnOff(): void
    {
        $this->status = false;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    abstract public function getDetails(): array;
}
