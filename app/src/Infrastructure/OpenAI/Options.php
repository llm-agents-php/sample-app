<?php

declare(strict_types=1);

namespace App\Infrastructure\OpenAI;

use LLM\Agents\LLM\OptionsInterface;
use Traversable;

readonly class Options implements OptionsInterface
{
    public function __construct(
        private array $options = [],
    ) {}

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->options);
    }

    public function has(string $option): bool
    {
        return isset($this->options[$option]);
    }

    public function get(string $option, mixed $default = null): mixed
    {
        return $this->options[$option] ?? $default;
    }

    public function with(string $option, mixed $value): static
    {
        $options = $this->options;
        $options[$option] = $value;

        return new static($options);
    }
}
