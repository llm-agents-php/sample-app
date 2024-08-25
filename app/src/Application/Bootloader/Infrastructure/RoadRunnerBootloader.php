<?php

declare(strict_types=1);

namespace App\Application\Bootloader\Infrastructure;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\RoadRunnerBridge\Bootloader as RoadRunnerBridge;

final class RoadRunnerBootloader extends Bootloader
{
    public function defineDependencies(): array
    {
        return [
            RoadRunnerBridge\LoggerBootloader::class,
//            RoadRunnerBridge\QueueBootloader::class,
//            RoadRunnerBridge\HttpBootloader::class,
            RoadRunnerBridge\CacheBootloader::class,
            RoadRunnerBridge\CentrifugoBootloader::class,
        ];
    }
}
