<?php

declare(strict_types=1);

namespace App\Domain\Chat\PromptGenerator;

use LLM\Agents\LLM\Prompt\Chat\MessagePrompt;
use LLM\Agents\LLM\Prompt\Chat\Prompt;
use LLM\Agents\LLM\Prompt\Chat\PromptInterface;
use LLM\Agents\PromptGenerator\Context;
use LLM\Agents\PromptGenerator\InterceptorHandler;
use LLM\Agents\PromptGenerator\PromptGeneratorInput;
use LLM\Agents\PromptGenerator\PromptInterceptorInterface;

final class SessionContextInjector implements PromptInterceptorInterface
{
    public function generate(
        PromptGeneratorInput $input,
        InterceptorHandler $next,
    ): PromptInterface {
        \assert($input->prompt instanceof Prompt);

        if (
            (!$input->context instanceof Context)
            || $input->context->getAuthContext() === null
        ) {
            return $next($input);
        }

        return $next(
            input: $input->withPrompt(
                $input->prompt
                    ->withAddedMessage(
                        MessagePrompt::system(
                            prompt: 'Session context: {active_context}',
                        ),
                    )->withValues(
                        values: [
                            'active_context' => \json_encode($input->context->getAuthContext()),
                        ],
                    ),
            ),
        );
    }
}
