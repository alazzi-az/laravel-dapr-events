<?php

return [
    'publisher' => [
        'middleware' => [
            \AlazziAz\DaprEventsPublisher\Middleware\AddCorrelationId::class,
            \AlazziAz\DaprEventsPublisher\Middleware\AddTenantContext::class,
            \AlazziAz\DaprEventsPublisher\Middleware\AddTimestamp::class,
        ],
    ],
];
