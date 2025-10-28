<?php

namespace AlazziAz\DaprEvents\Support;

class Subscription
{
    public function __construct(
        public readonly string $event,
        public readonly string $topic,
        public readonly string $route,
        public readonly string $pubsubName,
        public readonly array $metadata = []
    ) {
    }

    public function toArray(): array
    {
        return [
            'pubsubname' => $this->pubsubName,
            'topic' => $this->topic,
            'route' => $this->route,
            'metadata' => empty($this->metadata) ? (object) [] : $this->metadata,
        ];
    }
}
