<?php

namespace AlazziAz\DaprEventsListener\Middleware;

use AlazziAz\DaprEventsListener\Consuming\ListenerContext;
use Closure;
use Illuminate\Support\Facades\Context;

class TenantHydratorMiddleware implements ListenerMiddleware
{
    public function handle(ListenerContext $context, Closure $next): mixed
    {
        $metadata = $context->metadata();
        $tenantId = $metadata['tenant_id'] ?? $metadata['header_x-tenant-id'] ?? null;

        if ($tenantId !== null) {
            Context::add('tenant_id', $tenantId);
        }

        return $next($context);
    }
}
