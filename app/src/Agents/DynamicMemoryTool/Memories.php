<?php

declare(strict_types=1);

namespace App\Agents\DynamicMemoryTool;

use LLM\Agents\Solution\SolutionMetadata;
use Ramsey\Uuid\UuidInterface;
use Traversable;

final class Memories implements \IteratorAggregate
{
    public function __construct(
        public readonly UuidInterface $uuid,
        /** @var array<SolutionMetadata> */
        public array $memories = [],
    ) {}

    public function addMemory(SolutionMetadata $metadata): void
    {
        $this->memories[] = $metadata;
    }

    public function getIterator(): Traversable
    {
        yield from $this->memories;
    }
}
