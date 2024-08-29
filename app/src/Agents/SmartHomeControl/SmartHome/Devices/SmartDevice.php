<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl\SmartHome\Devices;

use App\Agents\SmartHomeControl\DeviceAction;

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

    abstract public function getControlSchema(): array;

    public function executeAction(DeviceAction $action, array $params): static
    {
        match ($action) {
            DeviceAction::TurnOn => $this->turnOn(),
            DeviceAction::TurnOff => $this->turnOff(),
            default => throw new \InvalidArgumentException("Unsupported action: {$action->value}"),
        };

        return $this;
    }
}
