<?php

declare(strict_types=1);

return [
    'default' => env('CACHE_STORAGE', 'rr-local'),

    'aliases' => [
        'chat-messages' => [
            'storage' => 'rr-local',
            'prefix' => 'chat:',
        ],
        'smart-home' => [
            'storage' => 'rr-local',
            'prefix' => 'smart-home:',
        ],
    ],

    'storages' => [
        'rr-local' => [
            'type' => 'roadrunner',
            'driver' => 'local',
        ],
    ],
];
