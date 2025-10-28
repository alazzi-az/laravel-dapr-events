# Quickstart

This guide demonstrates two Laravel services communicating via Dapr Pub/Sub using the Laravel Dapr Events packages. `orders-api` publishes events, while `notifications-api` consumes them.

## 1. Install and bootstrap both services

In **each** Laravel project:

```bash
composer require dapr/php-sdk:dev-main --prefer-stable --ignore-platform-reqs
composer require alazziaz/laravel-dapr-events --ignore-platform-reqs 
php artisan dapr-events:install
```

Add the subscriptions route macro (typically in `routes/api.php`):

```php
use AlazziAz\DaprEvents\Support\RouteMacros;

Route::daprSubscriptions();
```

Adjust `config/dapr-events.php` if you use a non-default Pub/Sub component name.

## 2. Configure Dapr Pub/Sub

Create a component (Redis example shown once and shared by both apps):

```yaml
apiVersion: dapr.io/v1alpha1
kind: Component
metadata:
  name: pubsub
spec:
  type: pubsub.redis
  version: v1
  metadata:
    - name: redisHost
      value: redis:6379
scopes:
  - orders-api
  - notifications-api
```

Ensure Redis (or the chosen backend) is running.

## 3. Publisher service (`orders-api`)

Define an event with a Dapr topic:

```php
// app/Events/OrderPlaced.php
use AlazziAz\DaprEvents\Attributes\Topic;

#[Topic('orders.placed')]
class OrderPlaced
{
    public function __construct(
        public string $orderId,
        public int $amountCents,
        public string $currency
    ) {}
}
```

Dispatch the event when business logic requires it:

```php
event(new OrderPlaced($orderId, $amount, 'USD'));

// or explicitly
app(\AlazziAz\DaprEventsPublisher\EventPublisher::class)
    ->publish(new OrderPlaced($orderId, $amount, 'USD'));
```

## 4. Consumer service (`notifications-api`)

Create a matching event class (copy the definition above into `notifications-api`) so the payload can be hydrated.

Generate a listener scaffold:

```bash
php artisan dapr-events:listener App\\Events\\OrderPlaced
```

Implement your handler (for example `app/Listeners/OrderPlacedListener.php`):

```php
class OrderPlacedListener
{
    public function handle(\App\Events\OrderPlaced $event): void
    {
        // send thank-you email, trigger workflow, etc.
    }
}
```

The listener package auto-discovers the `#[Topic]` attribute on the event class and exposes the route through `/dapr/subscribe`.

## 5. Run services with Dapr sidecars

Start Laravel and Dapr for each app in separate terminals:

```bash
# orders-api
dapr run --app-id orders-api --app-port 8000 --resources-path ./components php artisan serve --host=0.0.0.0 --port=8000

# notifications-api
dapr run --app-id notifications-api --app-port 8001 --resources-path ./components php artisan serve --host=0.0.0.0 --port=8001
```

Visit `GET /dapr/subscribe` on each app to verify discovered topics. The consumer should return:

```json
[
  {
    "pubsubname": "pubsub",
    "topic": "orders.placed",
    "route": "dapr/ingress/orders/placed",
    "metadata": []
  }
]
```

When `orders-api` dispatches `OrderPlaced`, Dapr forwards the CloudEvent to `notifications-api` via `POST /dapr/ingress/orders/placed`, and the listener re-emits the Laravel event.

## 6. Security and retries

- Enable request verification by setting `dapr-events.http.verify_signature` and `DAPR_INGRESS_SECRET`.
- Configure retry/backoff/dead-letter policies on the Dapr component; the provided `RetryOnceMiddleware` one-shot retry if you need PHP-level safety.

## 7. Testing

- Use `\AlazziAz\DaprEventsPublisher\Testing\DaprEventFake` to assert that publishing occurred inside feature tests.
- Simulate ingress posts in the consumer with Pest’s HTTP helpers (`packages/dapr-events-listener/tests/IngressTest.php` contains a reference implementation).
