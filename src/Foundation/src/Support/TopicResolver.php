<?php

namespace AlazziAz\DaprEvents\Support;

use AlazziAz\DaprEvents\Attributes\Topic;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

class TopicResolver
{
    public function __construct(
        protected Repository $config
    ) {
    }

    public function resolve(object|string $event): string
    {
        $className = is_object($event) ? $event::class : ltrim($event, '\\');

        if ($fromConfig = $this->fromConfig($className)) {
            return $fromConfig;
        }

        if (is_object($event) && ($fromAttribute = $this->fromAttribute($event))) {
            return $fromAttribute;
        }

        if ($fromClassAttribute = $this->fromClassAttribute($className)) {
            return $fromClassAttribute;
        }

        return $this->slugFromClassName($className);
    }

    public function resolveForListener(string $listenerClass, ?string $method = null): ?string
    {
        if ($method !== null) {
            $reflection = new ReflectionMethod($listenerClass, $method);
            $attribute = $this->findTopicAttribute($reflection->getAttributes(Topic::class));
            if ($attribute) {
                return $attribute->name;
            }
        }

        $reflection = new ReflectionClass($listenerClass);
        $attribute = $this->findTopicAttribute($reflection->getAttributes(Topic::class));

        return $attribute?->name;
    }

    protected function fromConfig(string $className): ?string
    {
        $topics = $this->config->get('dapr-events.topics', []);

        return $topics[$className] ?? null;
    }

    protected function fromAttribute(object $event): ?string
    {
        $reflection = new ReflectionClass($event);
        $attribute = $this->findTopicAttribute($reflection->getAttributes(Topic::class));

        return $attribute?->name;
    }

    protected function fromClassAttribute(string $className): ?string
    {
        $reflection = new ReflectionClass($className);
        $attribute = $this->findTopicAttribute($reflection->getAttributes(Topic::class));

        return $attribute?->name;
    }

    protected function findTopicAttribute(array $attributes): ?Topic
    {
        /** @var ReflectionAttribute<Topic> $attribute */
        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();

            if (! empty($instance->name)) {
                return $instance;
            }
        }

        return null;
    }

    protected function slugFromClassName(string $className): string
    {
        $shortName = class_basename($className);
        $slug = Str::snake($shortName, '.');

        return strtolower(str_replace(['\\', '$'], ['.', '.'], $slug));
    }
}
