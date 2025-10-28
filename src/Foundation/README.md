# Laravel Dapr Foundation

[![Packagist Version](https://img.shields.io/packagist/v/alazziaz/laravel-dapr-foundation.svg?color=0f6ab4)](https://packagist.org/packages/alazziaz/laravel-dapr-foundation)
[![Total Downloads](https://img.shields.io/packagist/dt/alazziaz/laravel-dapr-foundation.svg)](https://packagist.org/packages/alazziaz/laravel-dapr-foundation)

Shared contracts, configuration, and service provider for bridging Laravel events with the Dapr Pub/Sub building block.

## Installation

```bash
composer require alazziaz/laravel-dapr-foundation
php artisan dapr-events:install
```

## Features

- `GET /dapr/subscribe` endpoint declared via `Route::daprSubscriptions()`.
- Topic resolution based on event class → dotted slug, with overrides via `#[Topic('custom.topic')]` or `config/dapr-events.php`.
- Queueable listener that republishes local Laravel events through the Dapr publisher (toggle with `publish_local_events`).
- Contracts for event payload serialization, topic resolution, and publisher bindings.
- Optional HMAC signature verification for inbound Dapr requests.

## Configuration

Publish the configuration and adjust as needed:

```php
return [
    'pubsub' => [
        'name' => env('DAPR_PUBSUB', 'pubsub'),
    ],
    'topics' => [
        // App\Events\OrderPlaced::class => 'orders.placed',
    ],
    'http' => [
        'prefix' => 'dapr',
        'verify_signature' => false,
        'signature_header' => 'x-dapr-signature',
    ],
    'serialization' => [
        'wrap_cloudevent' => true,
    ],
    'publish_local_events' => true,
];
```

## Artisan commands

- `dapr-events:install` – publish config and listener stub.
- `dapr-events:list` – display discovered subscriptions and routes (supports `--json`).

## Usage

Add the route macro (typically in `routes/api.php`):

```php
use AlazziAz\DaprEvents\Support\RouteMacros;

Route::daprSubscriptions();
```

With the publisher/listener packages installed, local Laravel events are automatically bridged to Dapr topics and inbound messages are re-dispatched as native events.

## PHP compatibility note

The upstream `dapr/php-sdk` currently exposes only `dev-main` builds and targets PHP 8.4. If your application is on PHP 8.2 or 8.3, you must either loosen `minimum-stability` (while keeping `prefer-stable: true`) or pin the SDK to a tagged release that supports your runtime until the project ships a stable 8.4-compatible version.
