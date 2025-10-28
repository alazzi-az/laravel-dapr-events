<?php

namespace AlazziAz\DaprEvents;

use AlazziAz\DaprEvents\Console\InstallCommand;
use AlazziAz\DaprEvents\Console\ListSubscriptionsCommand;
use AlazziAz\DaprEvents\Contracts\EventPublisher as EventPublisherContract;
use AlazziAz\DaprEvents\Support\CloudEventFactory;
use AlazziAz\DaprEvents\Support\EventPayloadSerializer;
use AlazziAz\DaprEvents\Support\IngressContext;
use AlazziAz\DaprEvents\Support\IngressSignatureVerifier;
use AlazziAz\DaprEvents\Support\RouteMacros;
use AlazziAz\DaprEvents\Support\SubscriptionRegistry;
use AlazziAz\DaprEvents\Support\TopicResolver;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use RuntimeException;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/dapr-events.php' => config_path('dapr-events.php'),
        ], 'dapr-events-config');

        RouteMacros::register();
        $this->loadRoutesFrom(__DIR__ . '/../routes/dapr.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                ListSubscriptionsCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../stubs/dapr-listener.stub' => $this->app->basePath('stubs/dapr-listener.stub'),
            ], 'dapr-events-stubs');
        }

        $this->registerLocalEventBridge();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/dapr-events.php', 'dapr-events');

        $this->app->singleton(TopicResolver::class);
        $this->app->singleton(EventPayloadSerializer::class);
        $this->app->singleton(CloudEventFactory::class);
        $this->app->singleton(SubscriptionRegistry::class, function ($app) {
            $registry = new SubscriptionRegistry(
                $app->make(TopicResolver::class),
                $app['config']
            );

            $registry->ensureConfigSubscriptions();

            return $registry;
        });

        $this->app->singleton(IngressContext::class);
        $this->app->singleton(IngressSignatureVerifier::class);

        $this->app->bindIf(EventPublisherContract::class, function () {
            throw new RuntimeException(
                'No Dapr event publisher is bound. Install alazziaz/dapr-events-publisher or bind your own.'
            );
        });
    }

    protected function registerLocalEventBridge(): void
    {
        if (!$this->app['config']->get('dapr-events.publish_local_events', true)) {
            return;
        }

        $register = function (Dispatcher $dispatcher) {
            // TODO: Enable when fixing looping issues.
//            $dispatcher->listen('*', PublishLocalEvent::class);
        };

        $this->app->resolving(Dispatcher::class, $register);

        if ($this->app->resolved('events')) {
            $register($this->app['events']);
        }
    }
}
