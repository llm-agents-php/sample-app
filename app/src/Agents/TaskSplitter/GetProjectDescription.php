<?php

declare(strict_types=1);

namespace App\Agents\TaskSplitter;

use LLM\Agents\Tool\PhpTool;

/**
 * @extends PhpTool<ProjectDescriptionInput>
 */
final class GetProjectDescription extends PhpTool
{
    public const NAME = 'get_project_description';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            inputSchema: ProjectDescriptionInput::class,
            description: 'Get the description of a project from the project management system.',
        );
    }

    public function execute(object $input): string
    {
        return json_encode([
            'uuid' => $input->uuid,
            'title' => 'Оплата услуг клиентом',
            'description' => <<<'TEXT'
**As a customer, I want to be able to:**

- **Choose a subscription plan during registration:**
  - When creating an account on the service's website, I should be offered a choice of different subscription plans.
  - Each plan should include a clear description of the services provided and their costs.

- **Subscribe to a plan using a credit card:**
  - After selecting a plan, I need to provide my credit card details for payment.
  - There should be an option to save my card details for automatic monthly payments.

- **Receive monthly payment notifications:**
  - I expect to receive notifications via email or through my personal account about upcoming subscription charges.
  - The notification should arrive a few days before the charge, giving me enough time to ensure sufficient funds are available.

- **Access all necessary documents in my personal account:**
  - All financial documents, such as receipts and invoices, should be available for download at any time in my personal account.

- **Cancel the service if needed:**
  - I should be able to easily cancel my subscription through my personal account without needing to make additional calls or contact customer support.

- **Add a new card if the current one expires:**
  - If my credit card expires, I want to easily add a new card to the system through my personal account.

- **Continue using the service after cancellation until the end of the paid period:**
  - If I cancel my subscription, I expect to continue using the service until the end of the already paid period.
TEXT
            ,
        ]);
    }
}
