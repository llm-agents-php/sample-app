<?php

declare(strict_types=1);

namespace App\Agents\SiteStatusChecker;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final readonly class GetDNSInfoInput
{
    public function __construct(
        #[Field(title: 'Domain', description: 'The domain name to look up DNS information for')]
        public string $domain,
    ) {}
}
