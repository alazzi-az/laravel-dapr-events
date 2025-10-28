<?php

namespace AlazziAz\DaprEventsListener\Consuming;

use AlazziAz\DaprEvents\Attributes\Topic;
use AlazziAz\DaprEvents\Support\SubscriptionRegistry;
use AlazziAz\DaprEvents\Support\TopicResolver;
use AlazziAz\DaprEventsListener\Support\ClassFinder;
use Illuminate\Support\Collection;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class SubscriptionDiscovery
{
    public function __construct(
        protected SubscriptionRegistry $subscriptions,
        protected TopicResolver $topics,
        protected ClassFinder $classes
    ) {
    }

    public function discover(): void
    {
        $this->registerAttributedEvents();
        $this->registerAttributedListeners();
    }

    protected function registerAttributedEvents(): void
    {
        $directories = [
            app_path('Events'),
        ];

        foreach ($directories as $directory) {
            foreach ($this->classes->within($directory) as $class) {
                try {
                    $reflection = new ReflectionClass($class);
                } catch (\ReflectionException) {
                    continue;
                }
                $attribute = $this->firstTopicAttribute($reflection->getAttributes(Topic::class));

                if ($attribute) {
                    $this->subscriptions->registerEvent($class, $attribute->name);
                }
            }
        }
    }

    protected function registerAttributedListeners(): void
    {
        $directories = [
            app_path('Listeners'),
        ];

        foreach ($directories as $directory) {
            foreach ($this->classes->within($directory) as $class) {
                try {
                    $reflection = new ReflectionClass($class);
                } catch (\ReflectionException) {
                    continue;
                }

                $topic = $this->firstTopicAttribute($reflection->getAttributes(Topic::class));
                $handle = $this->resolveHandleMethod($reflection);

                if (! $handle) {
                    continue;
                }

                $methodTopic = $this->firstTopicAttribute($handle->getAttributes(Topic::class));
                if ($methodTopic) {
                    $topic = $methodTopic;
                }

                $parameter = $this->resolveEventParameter($handle);

                if (! $parameter) {
                    continue;
                }

                $eventClass = $parameter->getType()?->getName();

                if (! $eventClass) {
                    continue;
                }

                $topicName = $topic?->name ?? $this->topics->resolve($eventClass);

                $this->subscriptions->registerEvent($eventClass, $topicName);
            }
        }
    }

    /**
     * @param array<int, ReflectionAttribute<Topic>> $attributes
     */
    protected function firstTopicAttribute(array $attributes): ?Topic
    {
        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();

            if ($instance->name) {
                return $instance;
            }
        }

        return null;
    }

    protected function resolveHandleMethod(ReflectionClass $class): ?ReflectionMethod
    {
        if ($class->hasMethod('handle')) {
            return $class->getMethod('handle');
        }

        if ($class->hasMethod('__invoke')) {
            return $class->getMethod('__invoke');
        }

        return null;
    }

    protected function resolveEventParameter(ReflectionMethod $method): ?ReflectionParameter
    {
        return Collection::make($method->getParameters())
            ->first(fn (ReflectionParameter $parameter) => $parameter->getType() && ! $parameter->getType()->isBuiltin());
    }
}
