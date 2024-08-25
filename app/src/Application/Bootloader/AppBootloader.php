<?php

declare(strict_types=1);

namespace App\Application\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;

final class AppBootloader extends Bootloader
{
    public function defineSingletons(): array
    {
        return [];
    }
}
