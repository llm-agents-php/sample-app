<?php

declare(strict_types=1);

namespace App\Infrastructure\CycleOrm\Repository;

use App\Domain\Chat\Session;
use App\Domain\Chat\SessionRepositoryInterface;
use Cycle\ORM\Select\Repository;
use LLM\Agents\Chat\Exception\SessionNotFoundException;
use LLM\Agents\Chat\SessionInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends Repository<Session>
 */
final class SessionRepository extends Repository implements SessionRepositoryInterface
{
    public const ROLE = Session::ROLE;

    public function findByUuid(UuidInterface $uuid): ?SessionInterface
    {
        return $this->findByPK($uuid);
    }

    public function getByUuid(UuidInterface $uuid): SessionInterface
    {
        $session = $this->findByUuid($uuid);

        if ($session === null) {
            throw new SessionNotFoundException(\sprintf('Session with UUID %s not found', $uuid));
        }

        return $session;
    }

    public function findOneLatest(): ?SessionInterface
    {
        return $this->select()->orderBy('createdAt', 'DESC')->fetchOne();
    }

    public function findAllLatest(int $limit = 3): iterable
    {
        return $this->select()->orderBy('createdAt', 'DESC')->limit($limit)->fetchAll();
    }
}
