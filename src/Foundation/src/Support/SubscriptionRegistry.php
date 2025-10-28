<?php

namespace AlazziAz\DaprEvents\Support;

use Illuminate\Contracts\Config\Repository;

class SubscriptionRegistry
{
    /**
     * @var array<string, Subscription>
     */
    protected array $subscriptions = [];

    /**
     * @var array<string, Subscription>
     */
    protected array $routes = [];

    protected string $pubsubName;

    public function __construct(
        protected TopicResolver $topics,
        protected Repository $config
    ) {
        $this->pubsubName = $config->get('dapr-events.pubsub.name', 'pubsub');
    }

    public function registerEvent(string $eventClass, ?string $topic = null, ?string $route = null, array $metadata = []): Subscription
    {
        $topicName = $topic ?? $this->topics->resolve($eventClass);
        $routeName = $route ?? $this->buildRouteName($topicName);

        return $this->register(new Subscription(
            $eventClass,
            $topicName,
            $routeName,
            $this->pubsubName,
            $metadata
        ));
    }

    public function register(Subscription $subscription): Subscription
    {
        $key = $subscription->event.'|'.$subscription->topic;
        $this->subscriptions[$key] = $subscription;
        $this->routes[$subscription->route] = $subscription;

        return $subscription;
    }

    public function ensureConfigSubscriptions(): void
    {
        $configured = $this->config->get('dapr-events.topics', []);

        foreach ($configured as $event => $topic) {
            $this->registerEvent($event, $topic);
        }
    }

    /**
     * @return array<Subscription>
     */
    public function all(): array
    {
        return array_values($this->subscriptions);
    }

    public function findByRoute(string $route): ?Subscription
    {
        return $this->routes[$route] ?? null;
    }

    public function asDaprPayload(): array
    {
        return array_map(fn (Subscription $subscription) => $subscription->toArray(), $this->all());
    }

    public function setPubsubName(string $name): void
    {
        $this->pubsubName = $name;
    }

    protected function buildRouteName(string $topic): string
    {
        $prefix = trim($this->config->get('dapr-events.http.prefix', 'dapr'), '/');
        $normalizedTopic = str_replace('.', '/', $topic);

        return $prefix.'/ingress/'.$normalizedTopic;
    }
}
