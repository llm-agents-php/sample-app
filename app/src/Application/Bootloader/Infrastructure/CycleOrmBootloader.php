<?php

declare(strict_types=1);

namespace App\Application\Bootloader\Infrastructure;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Cycle\Bootloader as CycleBridge;
use Spiral\DatabaseSeeder\Bootloader\DatabaseSeederBootloader;

final class CycleOrmBootloader extends Bootloader
{
    public function defineDependencies(): array
    {
        return [
            CycleBridge\DatabaseBootloader::class,
            CycleBridge\MigrationsBootloader::class,
            CycleBridge\SchemaBootloader::class,
            CycleBridge\CycleOrmBootloader::class,
            CycleBridge\AnnotatedBootloader::class,
        ];
    }
}
