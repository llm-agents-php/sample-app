<?php

declare(strict_types=1);

namespace App\Application\Bootloader;

use App\Agents\DynamicMemoryTool\DynamicMemoryTool;
use App\Infrastructure\RoadRunner\SmartHome\DeviceStateManager;
use LLM\Agents\Agent\AgentRepositoryInterface;
use LLM\Agents\Agent\SmartHomeControl\SmartHome\Devices\Light;
use LLM\Agents\Agent\SmartHomeControl\SmartHome\Devices\SmartAppliance;
use LLM\Agents\Agent\SmartHomeControl\SmartHome\Devices\Thermostat;
use LLM\Agents\Agent\SmartHomeControl\SmartHome\Devices\TV;
use LLM\Agents\Agent\SmartHomeControl\SmartHome\DeviceStateRepositoryInterface;
use LLM\Agents\Agent\SmartHomeControl\SmartHome\DeviceStateStorageInterface;
use LLM\Agents\Agent\SmartHomeControl\SmartHome\SmartHomeSystem;
use LLM\Agents\Agent\SmartHomeControl\SmartHomeControlAgent;
use LLM\Agents\Solution\ToolLink;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Cache\CacheStorageProviderInterface;

final class SmartHomeBootloader extends Bootloader
{
    public function defineSingletons(): array
    {
        return [
            DeviceStateManager::class => static function (
                CacheStorageProviderInterface $provider,
            ): DeviceStateManager {
                return new DeviceStateManager($provider->storage('smart-home'));
            },

            DeviceStateStorageInterface::class => DeviceStateManager::class,
            DeviceStateRepositoryInterface::class => DeviceStateManager::class,

            SmartHomeSystem::class => static function (
                DeviceStateStorageInterface $stateStorage,
                DeviceStateRepositoryInterface $stateRepository,
            ): SmartHomeSystem {
                $smartHome = new SmartHomeSystem(
                    stateStorage: $stateStorage,
                    stateRepository: $stateRepository,
                );

                // Living Room Devices
                $livingRoomAirConditioner = new SmartAppliance(
                    'LR_AC_01',
                    'Living Room Air Conditioner',
                    'living_room',
                    'air_conditioner',
                    [
                        'temperature' => 0,
                        'mode' => 'cool',
                    ],
                );
                $livingRoomMainLight = new Light('LR_MAIN_01', 'Living Room Main Light', 'living_room', 'dimmable');
                $livingRoomTableLamp = new Light('LR_LAMP_01', 'Living Room Table Lamp', 'living_room', 'color');
                $livingRoomThermostat = new Thermostat('LR_THERM_01', 'Living Room Thermostat', 'living_room', 24);
                $livingRoomTV = new TV('LR_TV_01', 'Living Room TV', 'living_room', 20, 'HDMI 1');
                $livingRoomFireplace = new SmartAppliance(
                    'LR_FIRE_01',
                    'Living Room Fireplace',
                    'living_room',
                    'fireplace',
                    [
                        'temperature' => 0,
                    ],
                );
                $livingRoomSpeaker = new SmartAppliance(
                    'LR_SPEAK_01',
                    'Living Room Smart Speaker',
                    'living_room',
                    'speaker',
                    [
                        'volume' => 0,
                        'radio_station' => 'Classical FM',
                    ],
                );

                // Kitchen Devices
                $kitchenMainLight = new Light('KT_MAIN_01', 'Kitchen Main Light', 'kitchen', 'dimmable');
                $kitchenPendantLights = new Light('KT_PEND_01', 'Kitchen Pendant Lights', 'kitchen', 'dimmable');
                $kitchenRefrigerator = new SmartAppliance(
                    'KT_FRIDGE_01',
                    'Smart Refrigerator',
                    'kitchen',
                    'refrigerator',
                    [
                        'temperature' => 37,
                        'mode' => 'normal',
                    ],
                );
                $kitchenOven = new SmartAppliance('KT_OVEN_01', 'Smart Oven', 'kitchen', 'oven');
                $kitchenCoffeeMaker = new SmartAppliance(
                    'KT_COFFEE_01', 'Smart Coffee Maker', 'kitchen', 'coffee_maker',
                );

                // Bedroom Devices
                $bedroomMainLight = new Light('BR_MAIN_01', 'Bedroom Main Light', 'bedroom', 'dimmable');
                $bedroomNightstandLeft = new Light('BR_NIGHT_L_01', 'Left Nightstand Lamp', 'bedroom', 'color');
                $bedroomNightstandRight = new Light('BR_NIGHT_R_01', 'Right Nightstand Lamp', 'bedroom', 'color');
                $bedroomThermostat = new Thermostat('BR_THERM_01', 'Bedroom Thermostat', 'bedroom', 68);
                $bedroomTV = new TV('BR_TV_01', 'Bedroom TV', 'bedroom', 15, 'HDMI 1');
                $bedroomCeilingFan = new SmartAppliance('BR_FAN_01', 'Bedroom Ceiling Fan', 'bedroom', 'fan');

                // Bathroom Devices
                $bathroomMainLight = new Light('BA_MAIN_01', 'Bathroom Main Light', 'bathroom', 'dimmable');
                $bathroomMirrorLight = new Light('BA_MIRROR_01', 'Bathroom Mirror Light', 'bathroom', 'color');
                $bathroomExhaustFan = new SmartAppliance('BA_FAN_01', 'Bathroom Exhaust Fan', 'bathroom', 'fan');
                $bathroomSmartScale = new SmartAppliance('BA_SCALE_01', 'Smart Scale', 'bathroom', 'scale');

                // Add all devices to the smart home system
                $smartHome->addDevice($livingRoomAirConditioner);
                $smartHome->addDevice($livingRoomMainLight);
                $smartHome->addDevice($livingRoomTableLamp);
                $smartHome->addDevice($livingRoomThermostat);
                $smartHome->addDevice($livingRoomTV);
                $smartHome->addDevice($livingRoomFireplace);
                $smartHome->addDevice($livingRoomSpeaker);

                $smartHome->addDevice($kitchenMainLight);
                $smartHome->addDevice($kitchenPendantLights);
                $smartHome->addDevice($kitchenRefrigerator);
                $smartHome->addDevice($kitchenOven);
                $smartHome->addDevice($kitchenCoffeeMaker);

                $smartHome->addDevice($bedroomMainLight);
                $smartHome->addDevice($bedroomNightstandLeft);
                $smartHome->addDevice($bedroomNightstandRight);
                $smartHome->addDevice($bedroomThermostat);
                $smartHome->addDevice($bedroomTV);
                $smartHome->addDevice($bedroomCeilingFan);

                $smartHome->addDevice($bathroomMainLight);
                $smartHome->addDevice($bathroomMirrorLight);
                $smartHome->addDevice($bathroomExhaustFan);
                $smartHome->addDevice($bathroomSmartScale);

                return $smartHome;
            },
        ];
    }

    public function boot(
        AgentRepositoryInterface $agents,
    ): void {
        /** @var SmartHomeControlAgent $agent */
        $agent = $agents->get(SmartHomeControlAgent::NAME);

        $agent->addAssociation(new ToolLink(name: DynamicMemoryTool::NAME));
    }
}
