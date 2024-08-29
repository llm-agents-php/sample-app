<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl\SmartHome;

use App\Agents\SmartHomeControl\DeviceAction;
use App\Agents\SmartHomeControl\SmartHome\Devices\SmartDevice;
use Carbon\CarbonInterval;
use Psr\SimpleCache\CacheInterface;
use Spiral\Core\Attribute\Singleton;

#[Singleton]
final class SmartHomeSystem
{
    /** @var array<string, SmartDevice> */
    private array $devices = [];

    public function __construct(
        private readonly CacheInterface $cache,
    ) {}

    public function addDevice(SmartDevice $device): void
    {
        $this->devices[$device->id] = $device;
    }

    public function getDevice(string $id): ?SmartDevice
    {
        $device = $this->cache->get('device_' . $id, $this->devices[$id] ?? null);

        return $device;
    }

    public function getRoomList(): array
    {
        $rooms = \array_unique(\array_map(fn($device) => $device->room, $this->devices));
        \sort($rooms);
        return $rooms;
    }

    public function getRoomDevices(string $room): array
    {
        return \array_filter($this->getCachedDevices(), static fn($device): bool => $device->room === $room);
    }

    private function getCachedDevices(): array
    {
        $devices = [];
        foreach ($this->devices as $id => $device) {
            $devices[$id] = $this->getDevice($id);
        }

        return \array_filter($devices);
    }

    public function controlDevice(string $id, DeviceAction $action, array $params = []): array
    {
        $device = $this->getDevice($id);
        if ($device === null) {
            return ['error' => 'Device not found'];
        }

        $this->cache->set(
            'device_' . $id,
            $device->executeAction($action, $params),
            CarbonInterval::hour(),
        );

        $this->cache->set('last_action', \time(), CarbonInterval::hour());

        return [
            'id' => $device->id,
            'name' => $device->name,
            'room' => $device->room,
            'type' => \get_class($device),
            'params' => $device->getDetails(),
        ];
    }

    public function getLastActionTime(): ?int
    {
        return $this->cache->get('last_action') ?? null;
    }
}
