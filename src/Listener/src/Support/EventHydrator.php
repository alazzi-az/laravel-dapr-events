<?php

namespace AlazziAz\DaprEventsListener\Support;

use ReflectionClass;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class EventHydrator
{
    public function make(string $class, array $payload): object
    {
        if (method_exists($class, 'fromPayload')) {
            return $class::fromPayload($payload);
        }

        $payload = $this->unwrapCloudEvent($payload);

        $reflection = new ReflectionClass($class);

        if (! $reflection->isInstantiable()) {
            throw new \InvalidArgumentException("Event class [$class] is not instantiable.");
        }

        $constructor = $reflection->getConstructor();

        if (! $constructor) {
            return new $class();
        }

        $flattened = Arr::dot($payload);

        $arguments = [];
        foreach ($constructor->getParameters() as $parameter) {
            $value = $this->resolveValue($flattened, $parameter->getName());

            if ($value !== null || $parameter->allowsNull()) {
                $arguments[] = $value;
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
                continue;
            }

            throw new \InvalidArgumentException(
                "Missing value for parameter [{$parameter->getName()}] of event [$class]."
            );
        }

        return $reflection->newInstanceArgs($arguments);
    }

    protected function unwrapCloudEvent(array $payload): array
    {
        if (isset($payload['specversion'], $payload['data']) && is_array($payload['data'])) {
            return $payload['data'];
        }

        return $payload;
    }

    protected function resolveValue(array $flattened, string $parameter): mixed
    {
        $candidates = [
            $parameter,
            Str::snake($parameter),
            Str::camel($parameter),
            Str::studly($parameter),
        ];

        foreach ($flattened as $key => $value) {
            $lastSegment = Str::afterLast($key, '.');

            if (in_array($lastSegment, $candidates, true)) {
                return $value;
            }
        }

        return null;
    }
}

