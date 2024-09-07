<?php

declare(strict_types=1);

namespace App\Agents\DynamicMemoryTool;

use App\Application\Entity\Uuid;
use LLM\Agents\Solution\MetadataType;
use LLM\Agents\Solution\SolutionMetadata;
use LLM\Agents\Tool\PhpTool;

final class DynamicMemoryTool extends PhpTool
{
    public const NAME = 'dynamic_memory';

    public function __construct(
        private readonly DynamicMemoryService $memoryService,
    ) {
        parent::__construct(
            name: self::NAME,
            inputSchema: DynamicMemoryInput::class,
            description: 'Use this tool to add or update a important memory about the user. This memory will be used in the future to provide a better experience.',
        );
    }

    public function execute(object $input): string
    {
        $metadata = new SolutionMetadata(
            type: MetadataType::Memory,
            key: 'user_memory',
            content: $input->preference,
        );

        $sessionUuid = Uuid::fromString($input->sessionId);
        $this->memoryService->addMemory($sessionUuid, $metadata);

        return 'Memory updated';
    }
}
