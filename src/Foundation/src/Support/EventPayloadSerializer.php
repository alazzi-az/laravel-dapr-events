<?php

namespace AlazziAz\DaprEvents\Support;

use AlazziAz\DaprEvents\Contracts\ProvidesPayload;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use JsonSerializable;

class EventPayloadSerializer
{
    public function serialize(object $event): array
    {
        if ($event instanceof ProvidesPayload) {
            return $event->toPayload();
        }

        if (method_exists($event, 'toPayload')) {
            $payload = $event->toPayload();
            if (is_array($payload)) {
                return $payload;
            }
        }

        if ($event instanceof Arrayable) {
            return $event->toArray();
        }

        if ($event instanceof JsonSerializable) {
            $json = $event->jsonSerialize();

            return is_array($json) ? $json : ['value' => $json];
        }

        if (method_exists($event, 'toArray')) {
            $payload = $event->toArray();
            if (is_array($payload)) {
                return $payload;
            }
        }

        return $this->objectToArray($event);
    }

    protected function objectToArray(object $event): array
    {
        $data = [];

        foreach ((array) $event as $key => $value) {
            $normalizedKey = $this->normalizePropertyKey($key);
            $data[$normalizedKey] = $this->normalizeValue($value);
        }

        return $data;
    }

    protected function normalizePropertyKey(string $key): string
    {
        if (str_contains($key, "\0")) {
            $parts = explode("\0", $key);

            return end($parts);
        }

        return $key;
    }

    protected function normalizeValue(mixed $value): mixed
    {
        return match (true) {
            $value instanceof Arrayable => $value->toArray(),
            $value instanceof JsonSerializable => $value->jsonSerialize(),
            is_object($value) => Arr::map($this->objectToArray($value), fn ($v) => $this->normalizeValue($v)),
            is_array($value) => Arr::map($value, fn ($v) => $this->normalizeValue($v)),
            default => $value,
        };
    }
}
