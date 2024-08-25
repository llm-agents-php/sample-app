<?php

declare(strict_types=1);

namespace App\Application\Bootloader\Infrastructure;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Bootloader as Framework;

final class SecurityBootloader extends Bootloader
{
    public function defineDependencies(): array
    {
        return [
            Framework\Security\EncrypterBootloader::class,
            Framework\Security\FiltersBootloader::class,
        ];
    }
}
