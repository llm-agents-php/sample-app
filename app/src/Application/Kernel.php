<?php

declare(strict_types=1);

namespace App\Application;

use LLM\Agents\Agent\SiteStatusChecker\Integrations\Spiral\SiteStatusCheckerBootloader;
use LLM\Agents\Agent\SmartHomeControl\Integrations\Spiral\SmartHomeControlBootloader;
use LLM\Agents\JsonSchema\Mapper\Integration\Spiral\SchemaMapperBootloader;
use LLM\Agents\OpenAI\Client\Integration\Spiral\OpenAIClientBootloader;
use Spiral\Boot\Bootloader\CoreBootloader;
use Spiral\DotEnv\Bootloader\DotenvBootloader;
use Spiral\Prototype\Bootloader\PrototypeBootloader;
use Spiral\Tokenizer\Bootloader\TokenizerListenerBootloader;

class Kernel extends \Spiral\Framework\Kernel
{
    public function defineSystemBootloaders(): array
    {
        return [
            CoreBootloader::class,
            DotenvBootloader::class,
            TokenizerListenerBootloader::class,
        ];
    }

    public function defineBootloaders(): array
    {
        return [
            // Infrastructure
            Bootloader\Infrastructure\LogsBootloader::class,
            Bootloader\Infrastructure\ConsoleBootloader::class,
            Bootloader\Infrastructure\RoadRunnerBootloader::class,
            Bootloader\Infrastructure\CloudStorageBootloader::class,
            Bootloader\Infrastructure\SecurityBootloader::class,
            Bootloader\Infrastructure\CycleOrmBootloader::class,
            // Prototyping
            PrototypeBootloader::class,

            // Application
            Bootloader\AppBootloader::class,
            Bootloader\EventsBootloader::class,
            Bootloader\AgentsBootloader::class,
            Bootloader\AgentsChatBootloader::class,
            Bootloader\PersistenceBootloader::class,
            Bootloader\SmartHomeBootloader::class,

            // Agents
            OpenAIClientBootloader::class,
            SchemaMapperBootloader::class,
            SiteStatusCheckerBootloader::class,
            SmartHomeControlBootloader::class,
        ];
    }
}
