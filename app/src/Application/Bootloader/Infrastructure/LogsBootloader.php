<?php

declare(strict_types=1);

namespace App\Application\Bootloader\Infrastructure;

use Spiral\Boot\AbstractKernel;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Bootloader as Framework;
use Spiral\Exceptions\ExceptionHandler;
use Spiral\Exceptions\Renderer\ConsoleRenderer;
use Spiral\Exceptions\Renderer\JsonRenderer;
use Spiral\Exceptions\Reporter\FileReporter;
use Spiral\Exceptions\Reporter\LoggerReporter;
use Spiral\Http\ErrorHandler\PlainRenderer;
use Spiral\Http\ErrorHandler\RendererInterface;
use Spiral\Http\Middleware\ErrorHandlerMiddleware\EnvSuppressErrors;
use Spiral\Http\Middleware\ErrorHandlerMiddleware\SuppressErrorsInterface;
use Spiral\Monolog\Bootloader\MonologBootloader;
use Spiral\Sentry\Bootloader\SentryReporterBootloader;

final class LogsBootloader extends Bootloader
{
    public function __construct(
        private readonly ExceptionHandler $handler,
    ) {
    }

    public function defineDependencies(): array
    {
        return [
            // Logging and exceptions handling
            MonologBootloader::class,

            Framework\SnapshotsBootloader::class,

            // Sentry and Data collectors
            Framework\DebugBootloader::class,
            Framework\Debug\LogCollectorBootloader::class,
            Framework\Debug\HttpCollectorBootloader::class,
        ];
    }

    public function defineBindings(): array
    {
        return [
            SuppressErrorsInterface::class => EnvSuppressErrors::class,
            RendererInterface::class => PlainRenderer::class,
        ];
    }

    public function init(AbstractKernel $kernel): void
    {
        // Register the console renderer, that will be used when the application
        // is running in the console.
        $this->handler->addRenderer(new ConsoleRenderer());

        $kernel->running(function (): void {
            // Register the JSON renderer, that will be used when the application is
            // running in the HTTP context and a JSON response is expected.
            $this->handler->addRenderer(new JsonRenderer());
        });
    }

    public function boot(LoggerReporter $logger, FileReporter $files): void
    {
        // Register the logger reporter, that will be used to log the exceptions using
        // the logger component.
        $this->handler->addReporter($logger);

        // Register the file reporter. It allows you to save detailed information about an exception to a file
        // known as snapshot.
        $this->handler->addReporter($files);
    }
}
