<?php

namespace AlazziAz\DaprEvents\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Topic
{
    public function __construct(
        public readonly string $name
    ) {
    }
}
