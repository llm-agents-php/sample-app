<?php

declare(strict_types=1);

namespace App\Application\Bootloader\Infrastructure;

use App\Infrastructure\Valinor\MapperBuilder;
use CuyZ\Valinor\Cache\FileSystemCache;
use CuyZ\Valinor\Mapper\TreeMapper;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\DirectoriesInterface;
use Spiral\Boot\Environment\AppEnvironment;

final class ValinorBootloader extends Bootloader
{
    public function defineSingletons(): array
    {
        return [
            TreeMapper::class => static fn(
                MapperBuilder $builder,
            ) => $builder->build(),
            MapperBuilder::class => static fn(
                DirectoriesInterface $dirs,
                AppEnvironment $env,
            ) => new MapperBuilder(
                cache: match ($env) {
                    AppEnvironment::Production => new FileSystemCache(
                        cacheDir: $dirs->get('runtime') . 'cache/valinor',
                    ),
                    default => null,
                },
            ),
        ];
    }
}
