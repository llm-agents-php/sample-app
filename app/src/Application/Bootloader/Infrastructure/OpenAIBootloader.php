<?php

declare(strict_types=1);

namespace App\Application\Bootloader\Infrastructure;

use App\Infrastructure\OpenAI\LLM;
use App\Infrastructure\OpenAI\Parsers\ChatResponseParser;
use App\Infrastructure\OpenAI\StreamResponseParser;
use GuzzleHttp\Client as HttpClient;
use LLM\Agents\LLM\LLMInterface;
use OpenAI\Contracts\ClientContract;
use OpenAI\Responses\Chat\CreateStreamedResponse;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\EnvironmentInterface;

final class OpenAIBootloader extends Bootloader
{
    public function defineSingletons(): array
    {
        return [
            LLMInterface::class => LLM::class,

            ClientContract::class => static fn(
                EnvironmentInterface $env,
            ): ClientContract => \OpenAI::factory()
                ->withApiKey($env->get('OPENAI_KEY'))
                ->withHttpHeader('OpenAI-Beta', 'assistants=v1')
                ->withHttpClient(
                    new HttpClient([
                        'timeout' => (int) $env->get('OPENAI_HTTP_CLIENT_TIMEOUT', 2 * 60),
                    ]),
                )
                ->make(),

            StreamResponseParser::class => static function (
                ChatResponseParser $chatResponseParser,
            ): StreamResponseParser {
                $parser = new StreamResponseParser();

                // Register parsers here
                $parser->registerParser(CreateStreamedResponse::class, $chatResponseParser);

                return $parser;
            },
        ];
    }
}
