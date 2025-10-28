<?php

namespace Example\App\Listeners;

use Example\App\Events\OrderPlaced;

class SendThankYouEmail
{
    public function handle(OrderPlaced $event): void
    {
        // Send a thank-you email or trigger downstream process
    }
}
