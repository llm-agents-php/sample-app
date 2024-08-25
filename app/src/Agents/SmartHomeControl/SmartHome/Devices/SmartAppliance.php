<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl\SmartHome\Devices;

final class SmartAppliance extends SmartDevice
{
    public function __construct(
        string $id,
        string $name,
        string $room,
        public readonly string $type,
        protected array $attributes = [],
    ) {
        parent::__construct($id, $name, $room);
    }

    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function getDetails(): array
    {
        return array_merge(
            ['status' => $this->status ? 'on' : 'off', 'type' => $this->type],
            $this->attributes,
        );
    }
}
