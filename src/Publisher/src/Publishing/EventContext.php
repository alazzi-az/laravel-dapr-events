<?php

namespace AlazziAz\DaprEventsPublisher\Publishing;

class EventContext
{
    public function __construct(
        protected object $event,
        protected string $topic,
        protected string $pubsubName,
        protected array $payload,
        protected array $metadata = []
    ) {
    }

    public function event(): object
    {
        return $this->event;
    }

    public function topic(): string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): void
    {
        $this->topic = $topic;
    }

    public function pubsubName(): string
    {
        return $this->pubsubName;
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function mergeMetadata(array $metadata): void
    {
        $this->metadata = array_merge($this->metadata, $metadata);
    }

    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }
}
