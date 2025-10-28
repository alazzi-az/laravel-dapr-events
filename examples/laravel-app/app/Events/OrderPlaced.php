<?php

namespace Example\App\Events;

use AlazziAz\DaprEvents\Attributes\Topic;

#[Topic('orders.placed')]
class OrderPlaced
{
    public function __construct(
        public string $orderId,
        public int $amountCents,
        public string $currency
    ) {
    }
}
