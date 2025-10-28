# Dapr Events Publisher

[![Packagist Version](https://img.shields.io/packagist/v/alazziaz/laravel-dapr-publisher.svg?color=0f6ab4)](https://packagist.org/packages/alazziaz/laravel-dapr-publisher)
[![Total Downloads](https://img.shields.io/packagist/dt/alazziaz/laravel-dapr-publisher.svg)](https://packagist.org/packages/alazziaz/laravel-dapr-publisher)

Helper classes and middleware pipeline for publishing Laravel events through the Dapr sidecar.

## Installation

```bash
composer require alazziaz/laravel-dapr-publisher
```

> Requires `alazziaz/laravel-dapr-foundation` which provides shared config and topic resolution.

## EventPublisher

```php
$publisher = app(\AlazziAz\DaprEventsPublisher\EventPublisher::class);
$publisher->publish(new App\Events\OrderPlaced($id, $amount));
```

Publishing behaviour:

- Resolves the topic via `TopicResolver` (`App\Events\OrderPlaced` → `order.placed`).
- Serializes the event payload (array, JsonSerializable, `ProvidesPayload`, or property map).
- Wraps the payload in a CloudEvent envelope (`serialization.wrap_cloudevent`).
- Sends the result via `DaprClient::publishEvent`.

## Middleware pipeline

Configure middleware in `config/dapr-events.php` under `publisher.middleware`:

- `AddCorrelationId` – propagates/creates a correlation ID and logs it.
- `AddTenantContext` – forwards `X-Tenant-ID` or the authenticated user's tenant identifier.
- `AddTimestamp` – stamps the publish time in RFC3339 format.

You can push additional middleware classes to mutate metadata or payloads before the publish call.

## Testing

Use the fake to assert publishing:

```php
$fake = \AlazziAz\DaprEventsPublisher\Testing\DaprEventFake::register(app());

event(new App\Events\OrderPlaced('123', 9900));

$fake->assertPublished(App\Events\OrderPlaced::class);
```

See `tests/EventPublisherTest.php` for more examples.
