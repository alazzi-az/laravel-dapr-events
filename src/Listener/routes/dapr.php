<?php

use AlazziAz\DaprEventsListener\Consuming\IngressController;
use Illuminate\Support\Facades\Route;

$prefix = trim(config('dapr-events.http.prefix', 'dapr'), '/');

Route::prefix($prefix)->group(function () {
    Route::post('/ingress/{topic?}', IngressController::class)
        ->where('topic', '.*')
        ->name('dapr.ingress');
});
