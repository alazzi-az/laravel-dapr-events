<?php

return [
    'pubsub' => [
        'name' => env('DAPR_PUBSUB', 'pubsub'),
    ],
    'topics' => [
        // Register custom topic overrides: Event::class => 'custom.topic',
    ],
    'http' => [
        'prefix' => 'dapr',
        'verify_signature' => false,
        'signature_header' => 'x-dapr-signature',
        'signature_secret' => env('DAPR_INGRESS_SECRET'),
    ],
    'serialization' => [
        'wrap_cloudevent' => true,
    ],
    'publish_local_events' => true,
    'listener' => [
        'concurrency' => 1,
        'middleware' => [
            // \AlazziAz\DaprEventsListener\Middleware\RetryOnceMiddleware::class,
        ],
    ],
    'publisher' => [
        'middleware' => [
            // \AlazziAz\DaprEventsPublisher\Middleware\AddCorrelationId::class,
        ],
    ],
];
