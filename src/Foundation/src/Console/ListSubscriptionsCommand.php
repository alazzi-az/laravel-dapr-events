<?php

namespace AlazziAz\DaprEvents\Console;

use AlazziAz\DaprEvents\Support\SubscriptionRegistry;
use Illuminate\Console\Command;

class ListSubscriptionsCommand extends Command
{
    protected $signature = 'dapr-events:list {--json : Output subscriptions as JSON}';

    protected $description = 'List the Laravel events exposed to Dapr Pub/Sub.';

    public function __construct(
        protected SubscriptionRegistry $subscriptions
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->subscriptions->ensureConfigSubscriptions();
        $subscriptions = $this->subscriptions->all();

        if ($this->option('json')) {
            $this->line(json_encode(array_map(fn ($subscription) => $subscription->toArray(), $subscriptions), JSON_PRETTY_PRINT));

            return self::SUCCESS;
        }

        if (empty($subscriptions)) {
            $this->components->warn('No Dapr subscriptions discovered.');

            return self::SUCCESS;
        }

        $rows = array_map(fn ($subscription) => [
            $subscription->event,
            $subscription->topic,
            $subscription->route,
            $subscription->pubsubName,
        ], $subscriptions);

        $this->table(['Event', 'Topic', 'Route', 'PubSub'], $rows);

        return self::SUCCESS;
    }
}
