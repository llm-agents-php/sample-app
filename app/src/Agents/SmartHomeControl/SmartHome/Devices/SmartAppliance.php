<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl\SmartHome\Devices;

use App\Agents\SmartHomeControl\DeviceAction;
use App\Agents\SmartHomeControl\DeviceParam;

class SmartAppliance extends SmartDevice
{
    public function __construct(
        string $id,
        string $name,
        string $room,
        public readonly string $type,
        protected array $attributes = [],
        bool $status = false,
    ) {
        parent::__construct($id, $name, $room, $status);
    }

    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * @param array<DeviceParam> $attributes
     */
    public function setAttributes(array $attributes): void
    {
        foreach ($attributes as $attribute) {
            $this->setAttribute($attribute->name, $attribute->value);
        }
    }

    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function getDetails(): array
    {
        return array_merge(
            $this->attributes,
            [
                'status' => $this->status ? 'on' : 'off',
                'type' => $this->type,
            ],
        );
    }

    public function getControlSchema(): array
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'action' => [
                    'type' => 'string',
                    'enum' => [
                        DeviceAction::TurnOn->value,
                        DeviceAction::TurnOff->value,
                        DeviceAction::SetAttribute->value,
                    ],
                ],
                'params' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => [
                            'type' => 'string',
                        ],
                        'value' => [
                            'type' => ['string', 'number', 'boolean'],
                        ],
                    ],
                    'required' => ['name', 'value',],
                ],
            ],
            'required' => ['action'],
        ];

        // Add specific schemas based on the appliance type
        switch ($this->type) {
            case 'fireplace':
                $schema['properties']['intensity'] = [
                    'type' => 'integer',
                    'minimum' => 1,
                    'maximum' => 5,
                ];
                break;
            case 'speaker':
                $schema['properties']['volume'] = [
                    'type' => 'integer',
                    'minimum' => 0,
                    'maximum' => 100,
                ];
                $schema['properties']['radio_station'] = [
                    'type' => 'string',
                    'enum' => ['rock', 'pop', 'jazz', 'classical', 'news', 'talk', 'sports'],
                ];
                $schema['properties']['playback'] = [
                    'type' => 'string',
                    'enum' => ['play', 'pause', 'stop', 'next', 'previous'],
                ];
                break;
            case 'fan':
                $schema['properties']['speed'] = [
                    'type' => 'integer',
                    'minimum' => 0,
                    'maximum' => 5,
                ];
                break;
        }

        return $schema;
    }

    public function executeAction(DeviceAction $action, array $params): static
    {
        match ($action) {
            DeviceAction::SetAttribute => $this->setAttributes($params),
            default => parent::executeAction($action, $params),
        };

        return $this;
    }
}
