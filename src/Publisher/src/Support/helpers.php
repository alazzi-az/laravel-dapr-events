<?php
use AlazziAz\DaprEvents\Contracts\EventPublisher;

if (! function_exists('dapr_publish')) {
    function dapr_publish(object $event, array $metadata = []): void
    {
        app(EventPublisher::class)->publish($event, $metadata);
    }
}