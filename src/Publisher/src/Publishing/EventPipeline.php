<?php

namespace AlazziAz\DaprEventsPublisher\Publishing;

use Closure;
use Illuminate\Pipeline\Pipeline;

class EventPipeline
{
    public function __construct(
        protected Pipeline $pipeline
    ) {
    }

    /**
     * @param array<int, class-string|callable> $middleware
     */
    public function send(EventContext $context, array $middleware): EventContext
    {
        return $this->pipeline
            ->send($context)
            ->through($middleware)
            ->then(fn (EventContext $processed) => $processed);
    }
}
