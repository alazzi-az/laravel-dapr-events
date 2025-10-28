<?php

namespace AlazziAz\DaprEventsPublisher\Middleware;

use AlazziAz\DaprEventsPublisher\Publishing\EventContext;
use Closure;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AddCorrelationId implements PublisherMiddleware
{
    public const CONTEXT_KEY = 'dapr_correlation_id';

    public function handle(EventContext $context, Closure $next): mixed
    {
        $metadata = $context->metadata();
        $correlationId = $metadata['correlation_id'] ?? $this->resolveCorrelationId();
        $metadata['correlation_id'] = $correlationId;

        $context->setMetadata($metadata);
        Log::withContext(['correlation_id' => $correlationId]);
        Context::add(self::CONTEXT_KEY, $correlationId);

        return $next($context);
    }

    protected function resolveCorrelationId(): string
    {
        $request = request();

        return $request->header('X-Correlation-ID', (string) Str::uuid());
    }
}
