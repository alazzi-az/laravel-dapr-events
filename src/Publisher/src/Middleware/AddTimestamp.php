<?php

namespace AlazziAz\DaprEventsPublisher\Middleware;

use AlazziAz\DaprEventsPublisher\Publishing\EventContext;
use Closure;
use Illuminate\Support\Carbon;

class AddTimestamp implements PublisherMiddleware
{
    public function handle(EventContext $context, Closure $next): mixed
    {
        $metadata = $context->metadata();
        $metadata['published_at'] = Carbon::now()->toRfc3339String();

        $context->setMetadata($metadata);

        return $next($context);
    }
}
