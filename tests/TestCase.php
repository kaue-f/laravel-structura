<?php

namespace Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use KaueF\Structura\Providers\ConsoleServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ConsoleServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }
}
