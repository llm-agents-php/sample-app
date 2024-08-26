<?php

declare(strict_types=1);

namespace App\Infrastructure\OpenAI\Parsers;

use App\Infrastructure\OpenAI\StreamChunkCallbackInterface;
use LLM\Agents\LLM\Response\Response;
use OpenAI\Contracts\ResponseStreamContract;

interface ParserInterface
{
    public function parse(ResponseStreamContract $stream, ?StreamChunkCallbackInterface $callback = null): Response;
}
