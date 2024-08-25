<?php

declare(strict_types=1);

namespace App\Application\Bootloader;

use App\Domain\Chat\EntityManagerInterface;
use App\Domain\Chat\SessionRepositoryInterface;
use App\Infrastructure\CycleOrm\Entity\SessionEntityManager;
use App\Infrastructure\CycleOrm\Repository\SessionRepository;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Spiral\Boot\Bootloader\Bootloader;

final class PersistenceBootloader extends Bootloader
{
    public function defineSingletons(): array
    {
        return [
            SessionRepositoryInterface::class => static fn(
                ORMInterface $orm,
            ) => new SessionRepository(new Select(orm: $orm, role: SessionRepository::ROLE)),

            EntityManagerInterface::class => SessionEntityManager::class,
        ];
    }
}
