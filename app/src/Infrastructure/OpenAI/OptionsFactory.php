<?php

declare(strict_types=1);

namespace App\Infrastructure\OpenAI;

use LLM\Agents\LLM\OptionsFactoryInterface;
use LLM\Agents\LLM\OptionsInterface;

final class OptionsFactory implements OptionsFactoryInterface
{
    public function create(): OptionsInterface
    {
        return new Options();
    }
}
