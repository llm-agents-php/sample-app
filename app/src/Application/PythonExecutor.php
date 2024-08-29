<?php

declare(strict_types=1);

namespace App\Application;

use LLM\Agents\Tool\ExecutorInterface;
use Spiral\Boot\DirectoriesInterface;
use Spiral\Files\FilesInterface;

final readonly class PythonExecutor implements ExecutorInterface
{
    public function __construct(
        private FilesInterface $files,
        private DirectoriesInterface $dirs,
        private string $pythonPath = 'python3',
    ) {}

    public function execute(string $code, object $input): string|\Stringable
    {
        $input = \json_encode($input);

        $this->files->ensureDirectory($dir = $this->dirs->get('runtime') . 'python');

        $dir = \realpath($dir);
        $file = $dir . '/' . md5(\microtime()) . '.py';

        $this->files->write($file, $code);

        try {
            $output = \shell_exec(\sprintf('%s %s', $this->pythonPath, $file));
        } finally {
            $this->files->delete($file);
        }

        return \trim($output);
    }
}
