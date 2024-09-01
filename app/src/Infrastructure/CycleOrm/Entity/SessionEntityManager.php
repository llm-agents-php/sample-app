<?php

declare(strict_types=1);

namespace App\Infrastructure\CycleOrm\Entity;

use App\Domain\Chat\EntityManagerInterface;
use LLM\Agents\Chat\SessionInterface;

final readonly class SessionEntityManager implements EntityManagerInterface
{
    public function __construct(
        private \Cycle\ORM\EntityManagerInterface $em,
    ) {}

    public function persist(SessionInterface ...$entities): self
    {
        foreach ($entities as $entity) {
            $this->em->persist($entity);
        }

        return $this;
    }

    public function delete(SessionInterface ...$entities): self
    {
        foreach ($entities as $entity) {
            $this->em->delete($entity);
        }

        return $this;
    }

    public function flush(): void
    {
        $this->em->run();
    }
}
