<?php

namespace AlazziAz\DaprEvents\Publishing\Listeners;

use AlazziAz\DaprEvents\Contracts\EventPublisher;
use AlazziAz\DaprEvents\Support\IngressContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;


class PublishLocalEvent implements ShouldQueue
{
    use  Queueable;

    public function __construct(
        protected EventPublisher $publisher,
        protected IngressContext $ingress,
    ) {
    }
    public function handle(mixed $event): null|bool
    {
        if ($this->ingress->isInbound($event)) {
            return null;
        }

        try {
            $this->publisher->publish($event);
        } catch (Throwable $throwable) {
            Log::error('Failed to publish event to Dapr.', [
                'event_class' => $event::class,
                'message' => $throwable->getMessage(),
            ]);

            throw $throwable;
        }

        return false;
    }

}
