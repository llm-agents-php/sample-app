<?php

declare(strict_types=1);

namespace App\Application\Bootloader\Infrastructure;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Distribution\Bootloader\DistributionBootloader;
use Spiral\Storage\Bootloader\StorageBootloader;

final class CloudStorageBootloader extends Bootloader
{
    public function defineDependencies(): array
    {
        return [
            StorageBootloader::class,
            DistributionBootloader::class,
        ];
    }
}
