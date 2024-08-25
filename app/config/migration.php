<?php

declare(strict_types=1);

use Cycle\Schema\Generator\Migrations\Strategy\MultipleFilesStrategy;

return [
    'directory' => directory('app') . 'database/Migration/',

    'table' => 'migrations',

    'safe' => env('APP_ENV') !== 'prod',

    'strategy' => MultipleFilesStrategy::class,

    'namespace' => 'Database\\Migration',
];
