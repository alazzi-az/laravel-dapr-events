# Listener Pipeline

Requests from the Dapr sidecar flow through the listener pipeline before the original Laravel event is dispatched.

1. `RetryOnceMiddleware` – wraps the remainder of the pipeline, re-running it exactly once when an exception occurs.
2. `CorrelatedMessageMiddleware` – restores correlation identifiers from message metadata for downstream logging/telemetry.
3. `TenantHydratorMiddleware` – attaches tenant context to Laravel's context facade so multitenant apps can hydrate services.

Override the pipeline by editing `config/dapr-events.php` (`listener.middleware`). Each middleware receives a mutable `ListenerContext` value object.
