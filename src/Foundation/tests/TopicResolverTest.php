<?php

use AlazziAz\DaprEvents\Attributes\Topic;
use AlazziAz\DaprEvents\Support\TopicResolver;
use Illuminate\Support\Facades\Config;

it('resolves topic from config override', function () {
    Config::set('dapr-events.topics', [
        ConfigDrivenEvent::class => 'custom.topic',
    ]);

    $resolver = app(TopicResolver::class);

    expect($resolver->resolve(ConfigDrivenEvent::class))->toBe('custom.topic');
});

it('resolves topic from attribute', function () {
    Config::set('dapr-events.topics', []);

    $resolver = app(TopicResolver::class);

    expect($resolver->resolve(new AttributedEvent()))->toBe('orders.created');
});

it('generates snake dotted fallback topic', function () {
    Config::set('dapr-events.topics', []);

    $resolver = app(TopicResolver::class);

    expect($resolver->resolve(FallbackEvent::class))->toBe('fallback.event');
});

#[Topic('orders.created')]
class AttributedEvent
{
}

class ConfigDrivenEvent
{
}

class FallbackEvent
{
}
