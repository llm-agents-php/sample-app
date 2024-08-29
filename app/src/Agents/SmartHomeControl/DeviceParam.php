<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final class DeviceParam
{
    public function __construct(
        #[Field(title: 'Attribute name', description: 'The name of the parameter')]
        public string $name,
        #[Field(title: 'Attribute value', description: 'The value of the parameter')]
        public string $value,
    ) {}
}
