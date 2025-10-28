<?php

namespace AlazziAz\DaprEventsListener\Middleware;

use AlazziAz\DaprEventsListener\Consuming\ListenerContext;
use Closure;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;

class CorrelatedMessageMiddleware implements ListenerMiddleware
{
    public function handle(ListenerContext $context, Closure $next): mixed
    {
        $metadata = $context->metadata();
        $correlationId = $metadata['correlation_id'] ?? $metadata['header_x-correlation-id'] ?? null;

        if ($correlationId) {
            Context::add('dapr_correlation_id', $correlationId);
            Log::withContext(['correlation_id' => $correlationId]);
        }

        return $next($context);
    }
}
