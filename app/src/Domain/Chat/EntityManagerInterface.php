<?php

declare(strict_types=1);

namespace App\Domain\Chat;

interface EntityManagerInterface
{
    public function persist(Session ...$entities): self;

    public function delete(Session ...$entities): self;

    public function flush(): void;
}
