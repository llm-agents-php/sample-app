<?php

declare(strict_types=1);

namespace App\Agents\CodeReviewer;

use LLM\Agents\Tool\PhpTool;

/**
 * @extends  PhpTool<ReviewInput>
 */
final class ReviewTool extends PhpTool
{
    public const NAME = 'submit_review';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            inputSchema: ReviewInput::class,
            description: 'Submit a code review for a pull request. Call this whenever you need to submit a code review for a pull request.',
        );
    }

    public function execute(object $input): string
    {
        // Implementation to submit code review
        return json_encode(['status' => 'OK']);
    }
}
