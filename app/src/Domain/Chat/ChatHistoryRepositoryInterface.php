<?php

declare(strict_types=1);

namespace App\Domain\Chat;

use App\Application\Entity\Uuid;

interface ChatHistoryRepositoryInterface
{
    public function getMessages(Uuid $sessionUuid): iterable;

    public function addMessage(Uuid $sessionUuid, object $message): void;
}
