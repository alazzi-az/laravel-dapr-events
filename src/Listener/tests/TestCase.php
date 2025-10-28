<?php

namespace AlazziAz\DaprEventsListener\Tests;

use AlazziAz\DaprEvents\ServiceProvider as BaseProvider;
use AlazziAz\DaprEventsListener\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            BaseProvider::class,
            ServiceProvider::class,
        ];
    }
}
