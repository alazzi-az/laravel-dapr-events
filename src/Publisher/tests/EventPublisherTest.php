<?php

use AlazziAz\DaprEventsPublisher\EventPublisher;
use Dapr\Client\DaprClient;
use Illuminate\Support\Facades\Config;
use Mockery as m;

beforeEach(function () {
    Config::set('dapr-events.publisher.middleware', []);
    Config::set('dapr-events.serialization.wrap_cloudevent', false);
});

it('publishes serialized payload to dapr', function () {
    $client = m::mock(DaprClient::class);

    $client->shouldReceive('publishEvent')
        ->once()
        ->with(
            'pubsub',
            'order.placed',
            ['orderId' => 42, 'amount' => 5999],
            []
        );

    $this->app->instance(DaprClient::class, $client);

    $publisher = $this->app->make(EventPublisher::class);
    $publisher->publish(new OrderPlaced(42, 5999));
});

it('runs configured middleware before publishing', function () {
    Config::set('dapr-events.publisher.middleware', [
        \AlazziAz\DaprEventsPublisher\Middleware\AddTimestamp::class,
    ]);

    $client = m::mock(DaprClient::class);
    $client->shouldReceive('publishEvent')
        ->once()
        ->withArgs(function ($pubsub, $topic, $payload, $metadata) {
            return $pubsub === 'pubsub'
                && $topic === 'order.placed'
                && isset($metadata['published_at'])
                && is_string($metadata['published_at']);
        });

    $this->app->instance(DaprClient::class, $client);

    $publisher = $this->app->make(EventPublisher::class);
    $publisher->publish(new OrderPlaced(42, 5999));
});

class OrderPlaced
{
    public function __construct(
        public int $orderId,
        public int $amount
    ) {
    }
}
