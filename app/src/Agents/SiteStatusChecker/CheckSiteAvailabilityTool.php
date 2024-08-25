<?php

declare(strict_types=1);

namespace App\Agents\SiteStatusChecker;

use App\Domain\Tool\PhpTool;

/**
 * @extends  PhpTool<CheckSiteAvailabilityInput>
 */
final class CheckSiteAvailabilityTool extends PhpTool
{
    public const NAME = 'check_site_availability';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            inputSchema: CheckSiteAvailabilityInput::class,
            description: 'This tool checks if a given URL is accessible and returns its HTTP status code and response time.',
        );
    }

    public function execute(object $input): string
    {
        $ch = \curl_init($input->url);
        \curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
        ]);

        $startTime = \microtime(true);
        $response = \curl_exec($ch);
        $endTime = \microtime(true);

        $statusCode = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $finalUrl = \curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $redirectCount = \curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
        $responseTime = \round(($endTime - $startTime) * 1000, 2);

        \curl_close($ch);

        $isOnline = $statusCode >= 200 && $statusCode < 400;

        return \json_encode([
            'status_code' => $statusCode,
            'response_time_ms' => $responseTime,
            'is_online' => $isOnline,
            'final_url' => $finalUrl,
            'redirect_count' => $redirectCount,
        ]);
    }
}
