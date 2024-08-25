<?php

declare(strict_types=1);

namespace App\Domain\Chat;

use App\Application\Entity\Json;
use LLM\Agents\LLM\Prompt\Chat\Prompt;
use LLM\Agents\LLM\Prompt\MessageInterface;
use Traversable;

final readonly class History extends Json implements \IteratorAggregate
{
    /**
     * @return Traversable<MessageInterface>
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator(
            $this->toPrompt()->getMessages(),
        );
    }

    public function isEmpty(): bool
    {
        return $this->data === [];
    }

    public function toPrompt(): Prompt
    {
        return Prompt::fromArray($this->data);
    }
}
