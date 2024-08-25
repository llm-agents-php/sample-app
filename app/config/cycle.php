<?php

declare(strict_types=1);

use Cycle\ORM\Collection\ArrayCollectionFactory;
return [
    'schema' => [
        'cache' => env('CYCLE_SCHEMA_CACHE', true),

        'defaults' => [],

        'collections' => [
            'default' => 'array',
            'factories' => ['array' => new ArrayCollectionFactory()],
        ],

        'generators' => null,
    ],

    'warmup' => env('CYCLE_SCHEMA_WARMUP', false),
];
