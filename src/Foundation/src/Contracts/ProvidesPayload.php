<?php

namespace AlazziAz\DaprEvents\Contracts;

interface ProvidesPayload
{
    /**
     * Return the array payload that should be published to Dapr.
     */
    public function toPayload(): array;
}
