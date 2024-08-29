<?php

declare(strict_types=1);

namespace App\Agents\Calculator;

use LLM\Agents\Tool\ExecutorAwareInterface;
use LLM\Agents\Tool\ExecutorInterface;
use LLM\Agents\Tool\Tool;
use LLM\Agents\Tool\ToolLanguage;

// This class is a tool that uses a code written in Python to perform mathematical calculations.
final class PythonCalculatorTool extends Tool implements ExecutorAwareInterface
{
    public const NAME = 'python_calculator';

    private const CODE = <<<'PYTHON'

PYTHON;

    private ?ExecutorInterface $executor = null;

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            inputSchema: PythonCalculatorInput::class,
            description: 'This tool generates Python code for mathematical calculations.',
        );
    }

    public function getLanguage(): ToolLanguage
    {
        return ToolLanguage::Python;
    }

    public function execute(object $input): string
    {
        if (!$this->executor instanceof ExecutorInterface) {
            throw new \RuntimeException('Executor is not set for the tool');
        }

        return $this->executor->execute(
            \sprintf(self::CODE, \json_encode($input)),
            $input,
        );
    }

    public function setExecutor(ExecutorInterface $executor): static
    {
        $this->executor = $executor;

        return $this;
    }
}
