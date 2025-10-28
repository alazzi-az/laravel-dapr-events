<?php

namespace AlazziAz\DaprEventsListener\Consuming;

use AlazziAz\DaprEvents\Support\IngressContext;
use AlazziAz\DaprEvents\Support\IngressSignatureVerifier;
use AlazziAz\DaprEvents\Support\Subscription;
use AlazziAz\DaprEvents\Support\SubscriptionRegistry;
use AlazziAz\DaprEventsListener\Support\EventHydrator;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Log;

class MessageProcessor
{
    public function __construct(
        protected SubscriptionRegistry $subscriptions,
        protected IngressSignatureVerifier $signature,
        protected EventHydrator $hydrator,
        protected Dispatcher $events,
        protected IngressContext $ingressContext,
        protected Pipeline $pipeline,
        protected Repository $config
    ) {
    }

    public function handle(Request $request, string $routeKey): array
    {
        if (! $this->signature->verify($request)) {
            abort(403, 'Invalid Dapr ingress signature.');
        }

        $subscription = $this->subscriptions->findByRoute($routeKey);

        if (! $subscription) {
            abort(404, "Unknown Dapr topic for route [$routeKey]");
        }

        $raw = $this->decodeRequest($request);
        $payload = $raw['data'] ?? $raw;

        $metadata = $this->extractMetadata($request, $raw);

        $event = $this->hydrator->make($subscription->event, $payload);
        $this->ingressContext->markInbound($event);

        $context = new ListenerContext(
            $subscription,
            $event,
            $payload,
            $metadata,
            $request
        );

        $middleware = $this->config->get('dapr-events.listener.middleware', []);

        $context = $this->pipeline
            ->send($context)
            ->through($middleware)
            ->then(function (ListenerContext $context) {
                $this->events->dispatch($context->event());

                Log::info('Dispatched Dapr event to Laravel listeners.', [
                    'event_class' => $context->event()::class,
                    'topic' => $context->subscription()->topic,
                    'metadata' => $context->metadata(),
                ]);

                return $context;
            });

        return [
            'status' => 'SUCCESS',
        ];
    }

    protected function decodeRequest(Request $request): array
    {
        $content = $request->getContent();

        if ($content === '') {
            return [];
        }

        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : [];
    }

    protected function extractMetadata(Request $request, array $raw): array
    {
        $metadata = [];

        if (isset($raw['extensions']) && is_array($raw['extensions'])) {
            $metadata = $raw['extensions'];
        }

        if (isset($raw['traceid'])) {
            $metadata['traceid'] = $raw['traceid'];
        }

        foreach ($request->headers->all() as $key => $values) {
            $metadata['header_'.$key] = implode(',', $values);
        }

        return $metadata;
    }
}
