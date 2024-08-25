<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl\SmartHome;

use App\Agents\SmartHomeControl\SmartHome\Devices\Light;
use App\Agents\SmartHomeControl\SmartHome\Devices\SmartAppliance;
use App\Agents\SmartHomeControl\SmartHome\Devices\SmartDevice;
use App\Agents\SmartHomeControl\SmartHome\Devices\Thermostat;
use App\Agents\SmartHomeControl\SmartHome\Devices\TV;
use Spiral\Core\Attribute\Singleton;

#[Singleton]
final class SmartHomeSystem
{
    /** @var array<string, SmartDevice> */
    private array $devices = [];

    public function addDevice(SmartDevice $device): void
    {
        $this->devices[$device->id] = $device;
    }

    public function getDevice(string $id): ?SmartDevice
    {
        return $this->devices[$id] ?? null;
    }

    public function getRoomList(): array
    {
        $rooms = \array_unique(\array_map(fn($device) => $device->room, $this->devices));
        \sort($rooms);
        return $rooms;
    }

    public function getRoomDevices(string $room): array
    {
        return \array_filter($this->devices, static fn($device): bool => $device->room === $room);
    }

    public function controlDevice(string $id, string $action, array $params = []): array
    {
        $device = $this->getDevice($id);
        if (!$device) {
            return ['error' => 'Device not found'];
        }

        switch ($action) {
            case 'turnOn':
                $device->turnOn();
                break;
            case 'turnOff':
                $device->turnOff();
                break;
            case 'setBrightness':
                if ($device instanceof Light && isset($params['brightness'])) {
                    $device->setBrightness($params['brightness']);
                }
                break;
            case 'setColor':
                if ($device instanceof Light && isset($params['color'])) {
                    $device->setColor($params['color']);
                }
                break;
            case 'setTemperature':
                if ($device instanceof Thermostat && isset($params['temperature'])) {
                    $device->setTemperature($params['temperature']);
                }
                break;
            case 'setThermostatMode':
                if ($device instanceof Thermostat && isset($params['mode'])) {
                    $device->setMode($params['mode']);
                }
                break;
            case 'setTVVolume':
                if ($device instanceof TV && isset($params['volume'])) {
                    $device->setVolume($params['volume']);
                }
                break;
            case 'setTVInput':
                if ($device instanceof TV && isset($params['input'])) {
                    $device->setInput($params['input']);
                }
                break;
            case 'setApplianceAttribute':
                if ($device instanceof SmartAppliance && isset($params['key'], $params['value'])) {
                    $device->setAttribute($params['key'], $params['value']);
                }
                break;
            default:
                return ['error' => 'Invalid action'];
        }

        return $device->getDetails();
    }
}
