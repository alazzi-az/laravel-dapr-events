<?php

use AlazziAz\DaprEvents\Support\SubscriptionRegistry;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->eventPath = app_path('Events/OrderPlaced.php');
    File::ensureDirectoryExists(dirname($this->eventPath));

    $eventStub = <<<'PHP'
    <?php

    namespace App\Events;

    use AlazziAz\DaprEvents\Attributes\Topic;

    #[Topic('orders.placed')]
    class OrderPlaced
    {
        public function __construct(
            public int $orderId,
            public int $amount
        ) {}
    }
    PHP;

    File::put($this->eventPath, $eventStub);
    class_exists(\App\Events\OrderPlaced::class);
});

afterEach(function () {
    if (File::exists($this->eventPath)) {
        File::delete($this->eventPath);
    }
});

it('dispatches incoming dapr messages as laravel events', function () {
    Event::fake();

    $payload = [
        'data' => [
            'orderId' => 77,
            'amount' => 1599,
        ],
        'extensions' => [
            'correlation_id' => 'abc-123',
        ],
    ];

    $response = $this->postJson('/dapr/ingress/orders/placed', $payload);

    $response->assertOk();

    Event::assertDispatched(\App\Events\OrderPlaced::class, function ($event) {
        return $event->orderId === 77 && $event->amount === 1599;
    });

    $registry = $this->app->make(SubscriptionRegistry::class);
    expect($registry->asDaprPayload())->toContain([
        'pubsubname' => 'pubsub',
        'topic' => 'orders.placed',
        'route' => 'dapr/ingress/orders/placed',
        'metadata' => [],
    ]);
});
