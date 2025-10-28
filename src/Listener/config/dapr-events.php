<?php

return [
    'listener' => [
        'middleware' => [
            \AlazziAz\DaprEventsListener\Middleware\RetryOnceMiddleware::class,
            \AlazziAz\DaprEventsListener\Middleware\CorrelatedMessageMiddleware::class,
            \AlazziAz\DaprEventsListener\Middleware\TenantHydratorMiddleware::class,
        ],
    ],
];
