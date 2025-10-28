<?php

namespace AlazziAz\DaprEventsListener\Consuming;

use AlazziAz\DaprEvents\Support\Subscription;
use Illuminate\Http\Request;

class ListenerContext
{
    protected int $attempts = 0;

    public function __construct(
        protected Subscription $subscription,
        protected object $event,
        protected array $payload,
        protected array $metadata,
        protected Request $request
    ) {
    }

    public function subscription(): Subscription
    {
        return $this->subscription;
    }

    public function event(): object
    {
        return $this->event;
    }

    public function setEvent(object $event): void
    {
        $this->event = $event;
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function mergeMetadata(array $metadata): void
    {
        $this->metadata = array_merge($this->metadata, $metadata);
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function attempts(): int
    {
        return $this->attempts;
    }

    public function incrementAttempts(): int
    {
        return ++$this->attempts;
    }
}
