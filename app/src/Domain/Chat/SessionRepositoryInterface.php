<?php

declare(strict_types=1);

namespace App\Domain\Chat;

use LLM\Agents\Chat\Exception\SessionNotFoundException;
use LLM\Agents\Chat\SessionInterface;
use Ramsey\Uuid\UuidInterface;

interface SessionRepositoryInterface
{
    public function forUpdate(): static;

    /**
     * Find a session by its UUID.
     * This method is useful for retrieving a specific session.
     */
    public function findByUuid(UuidInterface $uuid): ?SessionInterface;

    /**
     * Get a session by its UUID.
     * This method is useful for retrieving a specific session.
     *
     * @throws SessionNotFoundException
     */
    public function getByUuid(UuidInterface $uuid): SessionInterface;
}
