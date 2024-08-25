<?php

declare(strict_types=1);

use Cycle\Database\Config;

return [
    'logger' => [
        'default' => null,
        'drivers' => [
            // 'runtime' => 'stdout'
        ],
    ],

    'default' => 'default',

    'databases' => [
        'default' => [
            'driver' => env('DB_CONNECTION', 'mysql'),
        ],
    ],

    'drivers' => [
        'mysql' => new Config\MySQLDriverConfig(
            connection: new Config\MySQL\TcpConnectionConfig(
                database: env('DB_DATABASE', 'llm'),
                host: env('DB_HOST', '127.0.0.1'),
                port: (int) env('DB_PORT', 3306),
                user: env('DB_USERNAME', 'root'),
                password: env('DB_PASSWORD'),
            ),
            queryCache: true,
        ),
    ],
];
