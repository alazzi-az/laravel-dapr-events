<?php

namespace AlazziAz\DaprEvents\Support;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CloudEventFactory
{
    public function __construct(
        protected Repository $config,
        protected TopicResolver $topics
    ) {
    }

    public function shouldWrap(): bool
    {
        return (bool) $this->config->get('dapr-events.serialization.wrap_cloudevent', true);
    }

    public function make(object $event, array $payload, array $metadata = []): array
    {
        $topic = $this->topics->resolve($event);

        $extensions = Arr::except($metadata, ['id', 'time', 'type', 'source', 'specversion']);

        return [
            'specversion' => '1.0',
            'id' => $metadata['id'] ?? (string) Str::uuid(),
            'source' => $metadata['source'] ?? config('app.url', 'laravel://dapr-events'),
            'type' => $metadata['type'] ?? $event::class,
            'time' => $this->formatTime($metadata['time'] ?? Carbon::now()),
            'datacontenttype' => 'application/json',
            'subject' => $topic,
            'data' => $payload,
            'extensions' => $extensions,
        ];
    }

    protected function formatTime(mixed $value): string
    {
        if ($value instanceof Carbon) {
            return $value->toRfc3339String();
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(\DateTimeInterface::RFC3339_EXTENDED);
        }

        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value)->toRfc3339String();
        }

        if (is_string($value)) {
            return Carbon::parse($value)->toRfc3339String();
        }

        return Carbon::now()->toRfc3339String();
    }
}
