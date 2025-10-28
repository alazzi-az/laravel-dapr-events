<?php

namespace AlazziAz\DaprEventsPublisher\Middleware;

use AlazziAz\DaprEventsPublisher\Publishing\EventContext;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AddTenantContext implements PublisherMiddleware
{
    public function handle(EventContext $context, Closure $next): mixed
    {
        $metadata = $context->metadata();
        $tenantId = $metadata['tenant_id'] ?? $this->resolveTenantId();

        if ($tenantId !== null) {
            $metadata['tenant_id'] = (string) $tenantId;
            $context->setMetadata($metadata);
        }

        return $next($context);
    }

    protected function resolveTenantId(): mixed
    {
        $request = Request::instance();

        if ($request && $request->headers->has('X-Tenant-ID')) {
            return $request->headers->get('X-Tenant-ID');
        }

        $user = Auth::user();

        if (! $user) {
            return null;
        }

        foreach (['tenant_id', 'tenantId', 'account_id'] as $property) {
            if (isset($user->{$property})) {
                return $user->{$property};
            }
        }

        if (method_exists($user, 'getTenantIdentifier')) {
            return $user->getTenantIdentifier();
        }

        return null;
    }
}
