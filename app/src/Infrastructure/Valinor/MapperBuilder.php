<?php

declare(strict_types=1);

namespace App\Infrastructure\Valinor;

use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder as BaseMapperBuilder;
use Psr\SimpleCache\CacheInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Spiral\Core\Attribute\Singleton;

#[Singleton]
final readonly class MapperBuilder
{
    public function __construct(
        private ?CacheInterface $cache,
    ) {}

    public function build(): TreeMapper
    {
        $builder = (new BaseMapperBuilder())
            ->infer(UuidInterface::class, fn() => Uuid::class)
            ->registerConstructor(Uuid::class, Uuid::fromString(...))
            ->enableFlexibleCasting()
            ->allowPermissiveTypes();

        if ($this->cache) {
            $builder = $builder->withCache($this->cache);
        }

        return $builder->mapper();
    }
}
