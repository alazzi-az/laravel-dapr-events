<?php

namespace AlazziAz\DaprEventsListener\Middleware;

use AlazziAz\DaprEventsListener\Consuming\ListenerContext;
use Closure;
use Illuminate\Support\Facades\Log;

class RetryOnceMiddleware implements ListenerMiddleware
{
    public function handle(ListenerContext $context, Closure $next): mixed
    {
        try {
            $context->incrementAttempts();

            return $next($context);
        } catch (\Throwable $throwable) {
            if ($context->attempts() >= 2) {
                throw $throwable;
            }

            Log::warning('Retrying Dapr message once after failure.', [
                'event_class' => $context->event()::class,
                'topic' => $context->subscription()->topic,
                'error' => $throwable->getMessage(),
            ]);

            return $next($context);
        }
    }
}
