<?php

return [
    'providers' => [
        'api-app' => [
            'driver' => 'applications'
        ]
    ],
    'guards' => [
        'api/v1' => [
            'driver' => 'jwt',
            'provider' => 'api-app'
        ]
    ]
];
