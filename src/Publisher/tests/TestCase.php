<?php

namespace AlazziAz\DaprEventsPublisher\Tests;

use AlazziAz\DaprEvents\ServiceProvider as BaseProvider;
use AlazziAz\DaprEventsPublisher\ServiceProvider;
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

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }
}
