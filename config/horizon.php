<?php

use Laravel\Horizon\Horizon;

return [
    'domain' => env('HORIZON_DOMAIN', null),

    'path' => env('HORIZON_PATH', 'horizon'),

    'use' => 'default',

    'prefix' => env('HORIZON_PREFIX', 'horizon:'),

    'middleware' => ['web', 'auth'],

    'waits' => [
        'redis:default' => 60,
        'redis:imports' => 120,
        'redis:reports' => 120,
    ],

    'trim' => [
        'recent' => 60,
        'pending' => 60,
        'completed' => 60,
        'recent_failed' => 10080,
        'failed' => 10080,
        'monitored' => 1440,
    ],

    'fast_termination' => false,

    'memory_limit' => 128,

    'defaults' => [
        'supervisor' => [
            'connection' => 'redis',
            'queue' => ['default'],
            'balance' => 'auto',
            'maxProcesses' => 3,
            'maxTime' => 0,
            'maxJobs' => 0,
            'force' => false,
            'backoff' => 3,
            'retry' => 3,
            'timeout' => 90,
            'nice' => 0,
        ],
    ],

    'environments' => [
        'production' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default','imports','reports'],
                'balance' => 'auto',
                'maxProcesses' => 10,
                'maxTime' => 0,
                'maxJobs' => 0,
                'force' => true,
                'timeout' => 120,
            ],
        ],
        'local' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default','imports','reports'],
                'balance' => 'simple',
                'maxProcesses' => 3,
                'force' => false,
            ],
        ],
    ],
];


