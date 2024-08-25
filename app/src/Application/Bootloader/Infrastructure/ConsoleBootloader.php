<?php

declare(strict_types=1);

namespace App\Application\Bootloader\Infrastructure;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Cycle\Bootloader as CycleBridge;
use Spiral\Bootloader as Framework;
use Spiral\Scaffolder\Bootloader\ScaffolderBootloader;
use Spiral\RoadRunnerBridge\Bootloader as RoadRunnerBridge;

final class ConsoleBootloader extends Bootloader
{
    public function defineDependencies(): array
    {
        return [
            Framework\CommandBootloader::class,
            ScaffolderBootloader::class,

            CycleBridge\CommandBootloader::class,
            CycleBridge\ScaffolderBootloader::class,

            RoadRunnerBridge\CommandBootloader::class,
            RoadRunnerBridge\ScaffolderBootloader::class,
        ];
    }
}
