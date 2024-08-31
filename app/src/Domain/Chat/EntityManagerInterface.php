<?php

declare(strict_types=1);

namespace App\Domain\Chat;

use LLM\Agents\Chat\SessionInterface;

interface EntityManagerInterface
{
    public function persist(SessionInterface ...$entities): self;

    public function delete(SessionInterface ...$entities): self;

    public function flush(): void;
}
