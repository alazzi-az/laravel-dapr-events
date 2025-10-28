# Example Laravel Application

This miniature application demonstrates how the Dapr Events packages plug into a Laravel codebase.

## Key files

- `app/Events/OrderPlaced.php` – event attributed with `#[Topic('orders.placed')]`.
- `app/Listeners/SendThankYouEmail.php` – standard Laravel listener reacting to the event.
- `routes/api.php` – registers the `/dapr/subscribe` endpoint via the supplied route macro.

## Running with Dapr

1. Install dependencies:

   ```bash
   composer install
   ```

2. Publish config and stubs:

   ```bash
   php artisan dapr-events:install
   ```

3. Start Laravel and Dapr sidecar (from the project root):

   ```bash
   dapr run --app-id orders-api --app-port 8000 --resources-path ./components php artisan serve
   ```

4. Dispatch the event somewhere in your codebase:

   ```php
   use Example\App\Events\OrderPlaced;

   event(new OrderPlaced('A-1001', 2599, 'USD'));
   ```

Dapr will pick up `GET /dapr/subscribe`, subscribe to the `orders.placed` topic, and deliver messages back to `POST /dapr/ingress/orders/placed`, which the listener package transforms into the native Laravel event so `SendThankYouEmail` runs.
