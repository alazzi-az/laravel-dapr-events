# Dapr Events Listener

Discover Dapr topics, expose `/dapr/subscribe`, and handle ingress HTTP calls by re-dispatching Laravel events.

## Installation

```bash
composer require alazziaz/laravel-dapr-listener
```

> Depends on `alazziaz/laravel-dapr-foundation` for shared infrastructure.

## Subscription discovery

The service provider scans `app/Events` and `app/Listeners` for the `#[Topic]` attribute and registers derived topics with the shared `SubscriptionRegistry`. Configured overrides in `config/dapr-events.php` are merged automatically.

Run `php artisan dapr-events:list` to inspect the discovered routes.

## HTTP ingress

Routes are registered under the configurable prefix (default `dapr`):

- `GET /dapr/subscribe` – list of `{pubsubname, topic, route}` subscriptions.
- `POST /dapr/ingress/{topic?}` – wildcard handler invoked by the Dapr sidecar.

Requests may be wrapped in CloudEvents; the listener extracts the payload, hydrates the Laravel event class, and dispatches it through the application event dispatcher. Inbound events are marked so they are not re-published.

## Listener middleware

Configured through `listener.middleware`:

- `RetryOnceMiddleware` – retries the pipeline a single time before surfacing the exception (causing Dapr to retry based on component settings).
- `CorrelatedMessageMiddleware` – restores correlation IDs from metadata for logging and downstream services.
- `TenantHydratorMiddleware` – exposes tenant context via the Laravel context facade.

## Artisan tooling

`php artisan dapr-events:listener App\\Events\\OrderPlaced` scaffolds a listener class in `app/Listeners`, defaulting to `OrderPlacedListener`.

The stub integrates cleanly with Laravel's native event system—no special base classes required.
