<?php

declare(strict_types=1);

return [
    array(
        'method' => 'GET',
        'route' => '/',
        'name' => 'root',
        'handler' => function ($request) {
            return [
                'message' => 'Hello from the root handler',
            ];
        },
        'middleware' => [],
    ),
];
