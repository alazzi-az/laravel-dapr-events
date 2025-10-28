<?php

namespace AlazziAz\DaprEvents\Contracts;

interface EventPublisher
{
    /**
     * Publish the given Laravel event through Dapr Pub/Sub.
     */
    public function publish(object $event, array $metadata = []): void;
}
