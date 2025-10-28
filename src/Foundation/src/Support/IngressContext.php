<?php

namespace AlazziAz\DaprEvents\Support;

use SplObjectStorage;

class IngressContext
{
    protected SplObjectStorage $inbound;

    public function __construct()
    {
        $this->inbound = new SplObjectStorage();
    }

    public function markInbound(object $event): void
    {
        $this->inbound->attach($event);
    }

    public function isInbound(object $event): bool
    {
        return $this->inbound->contains($event);
    }
}
