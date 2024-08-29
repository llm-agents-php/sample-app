<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl;

enum DeviceAction: string
{
    case TurnOn = 'turnOn';
    case TurnOff = 'turnOff';
    case SetBrightness = 'setBrightness';
    case SetColor = 'setColor';
    case SetTemperature = 'setTemperature';
    case SetMode = 'setMode';
    case SetVolume = 'setVolume';
    case SetInput = 'setInput';
    case SetAttribute = 'setAttribute';
}
