<?php

namespace AlazziAz\DaprEventsListener\Middleware;

use AlazziAz\DaprEventsListener\Consuming\ListenerContext;
use Closure;

interface ListenerMiddleware
{
    public function handle(ListenerContext $context, Closure $next): mixed;
}
