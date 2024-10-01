<?php

declare(strict_types=1);

namespace App\Agents\Calculator;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final class PythonCalculatorInput
{
    public function __construct(
        #[Field(title: 'Operation', description: 'The mathematical operation to perform (add, subtract, multiply, divide, average, sqrt)')]
        public string $operation,

        /**
         * @var array<int>
         */
        #[Field(title: 'Numbers', description: 'An array of numbers to perform the operation on')]
        public array $numbers,
    ) {}
}
