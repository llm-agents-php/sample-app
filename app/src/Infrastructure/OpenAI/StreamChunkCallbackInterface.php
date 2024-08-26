<?php

declare(strict_types=1);

namespace App\Infrastructure\OpenAI;

interface StreamChunkCallbackInterface
{
    public function __invoke(?string $chunk, bool $stop, ?string $finishReason = null): void;
}
