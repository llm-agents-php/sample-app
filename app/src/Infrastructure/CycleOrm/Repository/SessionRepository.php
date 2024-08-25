<?php

declare(strict_types=1);

namespace App\Infrastructure\CycleOrm\Repository;

use App\Application\Entity\Uuid;
use App\Domain\Chat\Session;
use App\Domain\Chat\SessionRepositoryInterface;
use Cycle\ORM\Select\Repository;
use LLM\Agents\Chat\Exception\SessionNotFoundException;

/**
 * @extends Repository<Session>
 */
final class SessionRepository extends Repository implements SessionRepositoryInterface
{
    public const ROLE = Session::ROLE;

    public function findByUuid(Uuid $uuid): ?Session
    {
        return $this->findByPK($uuid);
    }

    public function getByUuid(Uuid $uuid): Session
    {
        $session = $this->findByUuid($uuid);

        if ($session === null) {
            throw new SessionNotFoundException(\sprintf('Session with UUID %s not found', $uuid));
        }

        return $session;
    }
}
