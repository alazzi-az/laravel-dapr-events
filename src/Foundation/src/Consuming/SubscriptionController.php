<?php

namespace AlazziAz\DaprEvents\Consuming;

use AlazziAz\DaprEvents\Support\SubscriptionRegistry;
use Illuminate\Http\JsonResponse;

class SubscriptionController
{
    public function __construct(
        protected SubscriptionRegistry $subscriptions
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $this->subscriptions->ensureConfigSubscriptions();

        return new JsonResponse($this->subscriptions->asDaprPayload());
    }
}
