<?php

declare(strict_types=1);

namespace App\Application\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\League\Event\Bootloader\EventBootloader;

final class EventsBootloader extends Bootloader
{
    public function defineDependencies(): array
    {
        return [
            EventBootloader::class,
        ];
    }

    public function defineSingletons(): array
    {
        return [

        ];
    }
}
