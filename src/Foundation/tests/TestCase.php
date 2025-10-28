<?php

namespace AlazziAz\DaprEvents\Tests;

use AlazziAz\DaprEvents\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }
}
