<?php

declare(strict_types=1);

namespace App\Agents\SiteStatusChecker;

use App\Domain\Tool\PhpTool;

/**
 * @extends  PhpTool<PerformPingTestInput>
 */
final class PerformPingTestTool extends PhpTool
{
    public const NAME = 'perform_ping_test';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            inputSchema: PerformPingTestInput::class,
            description: 'This tool performs a ping test to a specified host and returns the results, including response times and packet loss.',
        );
    }

    public function execute(object $input): string
    {
        // Implement the actual ping test here
        // This is a placeholder implementation
        $command = \sprintf('ping -c %d %s', 4, \escapeshellarg($input->host));
        \exec($command, $output, $returnVar);

        $packetLoss = 0;
        $avgRoundTripTime = 0;

        foreach ($output as $line) {
            if (str_contains($line, 'packet loss')) {
                \preg_match('/(\d+(?:\.\d+)?)%/', $line, $matches);
                $packetLoss = $matches[1] ?? 0;
            }

            if (str_contains($line, 'rtt min/avg/max')) {
                \preg_match('/= [\d.]+\/([\d.]+)\/[\d.]+/', $line, $matches);
                $avgRoundTripTime = $matches[1] ?? 0;
            }
        }

        return \json_encode([
            'packet_loss_percentage' => (float) $packetLoss,
            'avg_round_trip_time_ms' => (float) $avgRoundTripTime,
            'success' => $returnVar === 0,
        ]);
    }
}
