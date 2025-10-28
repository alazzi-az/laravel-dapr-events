<?php

namespace AlazziAz\DaprEventsPublisher;

use AlazziAz\DaprEvents\Contracts\EventPublisher as EventPublisherContract;
use AlazziAz\DaprEventsPublisher\Publishing\EventPipeline;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/dapr-events.php', 'dapr-events');

        $this->app->singleton(EventPipeline::class, function ($app) {
            return new EventPipeline($app->make(Pipeline::class));
        });

        $this->app->singleton(EventPublisher::class);
        $this->app->alias(EventPublisher::class, EventPublisherContract::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/dapr-events.php' => config_path('dapr-events.php'),
            ], 'dapr-events-config');
        }
    }
}
