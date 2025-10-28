<?php

namespace AlazziAz\DaprEventsPublisher\Testing;

use AlazziAz\DaprEvents\Contracts\EventPublisher as EventPublisherContract;
use Illuminate\Contracts\Foundation\Application;
use PHPUnit\Framework\Assert;

class DaprEventFake implements EventPublisherContract
{
    /**
     * @var array<int, array{event: object, metadata: array}>
     */
    protected array $events = [];

    public static function register(Application $app): self
    {
        $fake = new self();
        $app->instance(EventPublisherContract::class, $fake);

        return $fake;
    }

    public function publish(object $event, array $metadata = []): void
    {
        $this->events[] = [
            'event' => $event,
            'metadata' => $metadata,
        ];
    }

    public function assertPublished(string $eventClass, ?callable $callback = null): void
    {
        $matched = array_filter($this->events, function ($item) use ($eventClass, $callback) {
            if (! $item['event'] instanceof $eventClass) {
                return false;
            }

            return $callback ? $callback($item['event'], $item['metadata']) : true;
        });

        Assert::assertNotEmpty($matched, "Failed asserting that event [$eventClass] was published to Dapr.");
    }

    public function assertNothingPublished(): void
    {
        Assert::assertEmpty($this->events, 'Failed asserting that no events were published to Dapr.');
    }
}
