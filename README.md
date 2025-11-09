# ⚠️ Package Deprecated — Moved to [alazziaz/laravel-dapr](https://github.com/alazzi-az/laravel-dapr)
# Laravel Dapr Events

[![Packagist Version](https://img.shields.io/packagist/v/alazziaz/laravel-dapr-events.svg?color=0f6ab4)](https://packagist.org/packages/alazziaz/laravel-dapr-events)
[![Total Downloads](https://img.shields.io/packagist/dt/alazziaz/laravel-dapr-events.svg)](https://packagist.org/packages/alazziaz/laravel-dapr-events)

Laravel-friendly tooling for publishing and consuming framework events over [Dapr Pub/Sub](https://docs.dapr.io/developing-applications/building-blocks/pubsub/), mirroring the developer ergonomics of while swapping RabbitMQ, kafka transport for the Dapr sidecar.
## Packages

- **`alazziaz/laravel-dapr-events`** – metapackage that installs all components in one go.
- **`alazziaz/laravel-dapr-foundation`** – shared contracts, service provider, config, and documentation. Publishes the `/dapr/subscribe` endpoint and bridges local Laravel events to Dapr.
- **`alazziaz/laravel-dapr-publisher`** – Dapr-backed publisher with middleware pipeline, CloudEvent wrapping, and testing fakes.
- **`alazziaz/laravel-dapr-listener`** – Subscription discovery, HTTP ingress controller, listener middleware, and artisan tooling to scaffold listeners.

Install the metapackage for the full experience:

```bash
composer require alazziaz/laravel-dapr-events
```
- **if you face any issues with php version, only add --ignore-platform-reqs flag to the above command this issue related to dapr-php-sdk package.**

## Highlights

- Automatically expose Laravel events to Dapr via `GET /dapr/subscribe`.
- Publish events with `event(new OrderPlaced(...))` or the explicit publisher service.
- Middleware pipelines on both publisher and listener sides for correlation IDs, tenancy, timestamps, and retries.
- Optional signature verification for ingress requests.
- Tests powered by Pest + Orchestra Testbench with a `DaprEventFake` for publisher assertions.
- Example Laravel application under `examples/laravel-app` to demonstrate end-to-end usage.

Refer to [`docs/quickstart.md`](docs/quickstart.md) for setup guidance, Dapr component examples, and workflow details.

### Example applications

Looking for a full end-to-end demo? Check out the companion repo: [mohammedazman/laravel-dapr-events-example](https://github.com/mohammedazman/laravel-dapr-events-example). It contains a pair of Laravel services wired up with these packages and ready to run against a Dapr sidecar.

## PHP compatibility with `dapr/php-sdk`

The official Dapr PHP SDK only ships development builds right now and its `dev-main` branch targets PHP 8.4. Until upstream tags a stable release, you have two practical options when installing these packages:

1. Allow dev dependencies for the SDK in your consuming application:
   ```json
   {
       "minimum-stability": "dev",
       "prefer-stable": true
   }
   ```
   or require the SDK explicitly with a dev constraint:
   ```php
   composer require dapr/php-sdk:dev-main --prefer-stable --ignore-platform-reqs
   ```
   or add to your `composer.json`:
   ```json
   {
       "require": {
           "dapr/php-sdk": "dev-main"
       },
       "prefer-stable": true
   }
   ```

2. Pin to a released SDK version (for example `^1.2`) if you can work with the APIs available there.

This requirement exists because the upstream Dapr SDK has not yet published a stable release that supports PHP 8.2/8.3.
