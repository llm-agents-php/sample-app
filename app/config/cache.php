<?php

declare(strict_types=1);

return [
    'default' => env('CACHE_STORAGE', 'rr-local'),

    'aliases' => [
        'chat-messages' => [
            'storage' => 'rr-local',
        ],
    ],

    'storages' => [
        'rr-local' => [
            'type' => 'roadrunner',
            'driver' => 'local',
        ],
    ],
];
