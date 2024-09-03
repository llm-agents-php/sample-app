<?php

declare(strict_types=1);

namespace App\Infrastructure\RoadRunner\SmartHome;

use Carbon\CarbonInterval;
use LLM\Agents\Agent\SmartHomeControl\SmartHome\Devices\SmartDevice;
use LLM\Agents\Agent\SmartHomeControl\SmartHome\DeviceStateRepositoryInterface;
use LLM\Agents\Agent\SmartHomeControl\SmartHome\DeviceStateStorageInterface;
use Psr\SimpleCache\CacheInterface;
use Spiral\Core\Attribute\Singleton;

#[Singleton]
final readonly class DeviceStateManager implements DeviceStateStorageInterface, DeviceStateRepositoryInterface
{
    private const DEVICE_STATE_TTL = 3600;
    private const LAST_ACTION_TTL = 3600;
    private const LAST_ACTION_KEY = 'last_action';

    public function __construct(
        private CacheInterface $cache,
    ) {}

    public function getDevice(string $id): ?SmartDevice
    {
        return $this->cache->get('device_' . $id);
    }

    public function update(SmartDevice $device): void
    {
        $this->cache->set(
            'device_' . $device->id,
            $device,
            CarbonInterval::seconds(self::DEVICE_STATE_TTL),
        );

        $this->cache->set(self::LAST_ACTION_KEY, \time(), CarbonInterval::seconds(self::LAST_ACTION_TTL));
    }

    public function getLastActionTime(): ?int
    {
        return $this->cache->get(self::LAST_ACTION_KEY) ?? null;
    }
}
