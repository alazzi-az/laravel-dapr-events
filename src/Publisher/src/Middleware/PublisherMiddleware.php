<?php

namespace AlazziAz\DaprEventsPublisher\Middleware;

use AlazziAz\DaprEventsPublisher\Publishing\EventContext;
use Closure;

interface PublisherMiddleware
{
    public function handle(EventContext $context, Closure $next): mixed;
}
