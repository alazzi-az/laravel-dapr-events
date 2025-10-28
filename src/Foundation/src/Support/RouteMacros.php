<?php

namespace AlazziAz\DaprEvents\Support;

use AlazziAz\DaprEvents\Consuming\SubscriptionController;
use Illuminate\Support\Facades\Route;

class RouteMacros
{
    public static function register(): void
    {
        if (! Route::hasMacro('daprSubscriptions')) {
            Route::macro('daprSubscriptions', function (?callable $within = null, array $options = []) {
                $prefix = trim(config('dapr-events.http.prefix', 'dapr'), '/');
                $middleware = $options['middleware'] ?? [];

                return Route::prefix($prefix)
                    ->middleware($middleware)
                    ->group(function () use ($within) {
                        Route::get('/subscribe', SubscriptionController::class)
                            ->name('dapr.subscribe');

                        if ($within) {
                            $within();
                        }
                    });
            });
        }
    }
}
