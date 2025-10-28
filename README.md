# Laravel Dapr Events

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
