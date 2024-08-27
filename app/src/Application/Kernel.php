<?php

declare(strict_types=1);

namespace App\Application;

use LLM\Agents\Agent\SiteStatusChecker\Bootloader\SiteStatusCheckerBootloader;
use LLM\Agents\OpenAI\Client\Bootloader\OpenAIClientBootloader;
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
            Bootloader\Infrastructure\ValinorBootloader::class,
            OpenAIClientBootloader::class,
            // Prototyping
            PrototypeBootloader::class,

            // Application
            Bootloader\AppBootloader::class,
            Bootloader\EventsBootloader::class,
            Bootloader\AgentsBootloader::class,
            Bootloader\PersistenceBootloader::class,
            Bootloader\SmartHomeBootloader::class,

            // Agents
            SiteStatusCheckerBootloader::class,
        ];
    }
}
