<?php

declare(strict_types=1);

namespace App\Agents\Calculator;

use LLM\Agents\Agent\AgentAggregate;
use LLM\Agents\Agent\Agent;
use LLM\Agents\OpenAI\Client\OpenAIModel;
use LLM\Agents\Solution\Model;
use LLM\Agents\Solution\ToolLink;
use LLM\Agents\Solution\MetadataType;
use LLM\Agents\Solution\SolutionMetadata;

// This class represents the Calculator Agent,
// which uses the Tool with uses python language to perform mathematical calculations.
final class CalculatorAgent extends AgentAggregate
{
    public const NAME = 'python_calculator_agent';

    public static function create(): self
    {
        $agent = new Agent(
            key: self::NAME,
            name: 'Python Calculator Agent',
            description: 'This agent generates Python code for mathematical calculations. It can handle addition, subtraction, multiplication, division, averaging, and square root operations.',
            instruction: <<<'INSTRUCTION'
You are a Python Calculator Agent.
Your primary goal is to assist users in performing arithmetic calculations by generating appropriate Python code.
When a user provides a mathematical expression or asks for a calculation, you should use the python_calculator tool to generate the code.
Always provide the generated Python code along with an explanation of how it solves the user\'s request.
If there are any potential errors or invalid inputs, explain them clearly to the user.
INSTRUCTION
            ,
        );

        $aggregate = new self($agent);

        $aggregate->addMetadata(
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'operation_mapping',
                content: "Map user requests to the correct operation: 'add' for addition, 'subtract' for subtraction, 'multiply' for multiplication, 'divide' for division, 'average' for averaging, and 'sqrt' for square root.",
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'number_formatting',
                content: 'Ensure that numbers are correctly formatted as a Python list when passing them to the tool.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'error_handling',
                content: 'Be aware of potential errors like division by zero or invalid operations, and explain these to the user if the generated code might raise such errors.',
            ),
            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'example_addition',
                content: 'Add 5, 10, and 15',
            ),
            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'example_division',
                content: 'Divide 100 by 5 and then by 2',
            ),
            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'example_square_root',
                content: 'Calculate the square root of 16',
            ),
        );

        $model = new Model(model: OpenAIModel::Gpt4oMini->value);
        $aggregate->addAssociation($model);

        $aggregate->addAssociation(new ToolLink(name: PythonCalculatorTool::NAME));

        return $aggregate;
    }
}
