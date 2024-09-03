<?php

declare(strict_types=1);

namespace App\Endpoint\Console;

use App\Infrastructure\RoadRunner\SmartHome\DeviceStateManager;
use LLM\Agents\Agent\SmartHomeControl\SmartHome\DeviceStateRepositoryInterface;
use LLM\Agents\Agent\SmartHomeControl\SmartHome\SmartHomeSystem;
use Spiral\Console\Attribute\AsCommand;
use Spiral\Console\Attribute\Option;
use Spiral\Console\Command;
use Symfony\Component\Console\Helper\Table;

#[AsCommand(
    name: 'smart-home:status',
    description: 'Display the status of the smart home'
)]
final class DisplaySmartHomeStatusCommand extends Command
{
    #[Option(name: 'interactive', description: 'Enable interactive mode')]
    public bool $interactive = false;

    public function __invoke(
        SmartHomeSystem $smartHome,
        DeviceStateRepositoryInterface $stateRepository,
    ): int {
        \assert($stateRepository instanceof DeviceStateManager);
        $lastUpdate = false;

        while (true) {
            if ($stateRepository->getLastActionTime() === $lastUpdate) {
                \sleep(1);
                continue;
            }

            $lastUpdate = $stateRepository->getLastActionTime();

            // Clear the console screen
            $this->output->write("\033\143");

            $rooms = $smartHome->getRoomList();
            foreach ($rooms as $room) {
                $this->output->writeln("<info>{$room}</info>");

                $devices = $smartHome->getRoomDevices($room);
                $table = new Table($this->output);
                $table->setHeaders(['Device', 'Status', 'Details']);

                foreach ($devices as $device) {
                    $status = $device->getStatus() ? '<fg=green>ON</>' : '<fg=red>OFF</>';
                    $details = $this->formatDetails($device->getDetails());
                    $table->addRow([$device->name, $status, $details]);
                }

                $table->render();
                $this->output->writeln('');
            }
        }

        return self::SUCCESS;
    }

    private function formatDetails(array $details): string
    {
        $formattedDetails = [];
        foreach ($details as $key => $value) {
            if ($key === 'status') {
                continue;
            }
            $formattedDetails[] = "{$key}: {$value}";
        }

        return \implode(', ', $formattedDetails);
    }
}
