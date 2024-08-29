<?php

declare(strict_types=1);

namespace App\Agents\SmartHomeControl;

use App\Agents\DynamicMemoryTool\DynamicMemoryTool;
use LLM\Agents\Agent\Agent;
use LLM\Agents\Agent\AgentAggregate;
use LLM\Agents\OpenAI\Client\OpenAIModel;
use LLM\Agents\OpenAI\Client\Option;
use LLM\Agents\Solution\MetadataType;
use LLM\Agents\Solution\Model;
use LLM\Agents\Solution\SolutionMetadata;
use LLM\Agents\Solution\ToolLink;

final class SmartHomeControlAgent extends AgentAggregate
{
    public const NAME = 'smart_home_control';

    public static function create(): self
    {
        $agent = new Agent(
            key: self::NAME,
            name: 'Smart Home Control Assistant',
            description: 'This agent manages and controls various smart home devices across multiple rooms, including lights, fireplaces, and TVs.',
            instruction: <<<'INSTRUCTION'
You are a Smart Home Control Assistant.
Your primary goal is to help users manage their smart home devices efficiently.
INSTRUCTION
            ,
        );

        $aggregate = new self($agent);

        $aggregate->addMetadata(
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'room_validation',
                content: 'Important! Before request devices in any room, use the get_room_list tool to know correct room names.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'energy_efficiency',
                content: 'Remember to suggest energy-efficient settings when appropriate.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'device_status',
                content: 'Important! Check device status before performing any action. Because a state can be changed by another user or system.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'home_name',
                content: 'We are currently in the "Home" home.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'store_important_memory',
                content: 'Store important information in memory for future reference. For example if user tells that he likes some specific setting, store it in memory.',
            ),

            new SolutionMetadata(
                type: MetadataType::Configuration,
                key: Option::MaxTokens->value,
                content: 3000,
            ),

            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'query_devices',
                content: 'What devices are in the living room?',
            ),
            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'control_light',
                content: 'Turn on the lights in the bedroom.',
            ),
            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'control_fireplace',
                content: 'Set the fireplace in the living room to medium intensity.',
            ),
            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'control_tv',
                content: 'Today is rainy. I\'m in the living room and in a bad mood, could you do something to cheer me up?',
            ),
            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'check_device_status',
                content: 'Is the kitchen light on?',
            ),
            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'control_multiple_devices',
                content: 'Turn off all devices in the master bedroom.',
            ),
        );

        $model = new Model(model: OpenAIModel::Gpt4oMini->value);
        $aggregate->addAssociation($model);

        $aggregate->addAssociation(new ToolLink(name: ListRoomDevicesTool::NAME));
        $aggregate->addAssociation(new ToolLink(name: GetDeviceDetailsTool::NAME));
        $aggregate->addAssociation(new ToolLink(name: ControlDeviceTool::NAME));
        $aggregate->addAssociation(new ToolLink(name: GetRoomListTool::NAME));
        $aggregate->addAssociation(new ToolLink(name: DynamicMemoryTool::NAME));

        return $aggregate;
    }
}
