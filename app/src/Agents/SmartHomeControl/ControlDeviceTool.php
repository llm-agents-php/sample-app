<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl;

use App\Agents\SmartHomeControl\SmartHome\SmartHomeSystem;
use App\Domain\Tool\PhpTool;

/**
 * @extends  PhpTool<ControlDeviceInput>
 */
final class ControlDeviceTool extends PhpTool
{
    public const NAME = 'control_device';

    public function __construct(
        private SmartHomeSystem $smartHome,
    ) {
        parent::__construct(
            name: self::NAME,
            inputSchema: ControlDeviceInput::class,
            description: 'Controls a specific device by performing the specified action with given parameters.',
        );
    }

    public function execute(object $input): string
    {
        $result = $this->smartHome->controlDevice($input->deviceId, $input->action, $input->params);

        if (isset($result['error'])) {
            return json_encode(['error' => $result['error']]);
        }

        return json_encode([
            'id' => $input->deviceId,
            'action' => $input->action,
        ]);
    }
}
