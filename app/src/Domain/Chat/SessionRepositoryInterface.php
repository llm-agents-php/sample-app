<?php

declare(strict_types=1);

namespace App\Domain\Chat;

use App\Application\Entity\Uuid;
use App\Domain\Chat\Exception\SessionNotFoundException;

interface SessionRepositoryInterface
{
    public function forUpdate(): static;

    /**
     * Find a session by its UUID.
     * This method is useful for retrieving a specific session.
     */
    public function findByUuid(Uuid $uuid): ?Session;

    /**
     * Get a session by its UUID.
     * This method is useful for retrieving a specific session.
     *
     * @throws SessionNotFoundException
     */
    public function getByUuid(Uuid $uuid): Session;
}
