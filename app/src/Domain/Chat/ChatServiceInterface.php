<?php

declare(strict_types=1);

namespace App\Domain\Chat;

use App\Application\Entity\Uuid;
use App\Domain\Chat\Exception\ChatNotFoundException;

interface ChatServiceInterface
{
    /**
     * Get session by UUID.
     *
     * @throws ChatNotFoundException
     */
    public function getSession(Uuid $sessionUuid): Session;

    public function updateSession(Session $session): void;

    /**
     * Start session on context.
     *
     * @return Uuid Session UUID
     */
    public function startSession(Uuid $accountUuid, string $agentName): Uuid;

    /**
     * Ask question to chat.
     *
     * @return Uuid Message UUID.
     */
    public function ask(Uuid $sessionUuid, string|\Stringable $message): Uuid;

    /**
     * Close session.
     */
    public function closeSession(Uuid $sessionUuid): void;
}
