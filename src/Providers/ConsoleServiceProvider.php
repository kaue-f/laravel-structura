<?php

namespace Structura\Providers;

use Illuminate\Support\ServiceProvider;
use Structura\Console\Commands\CacheCreation;
use Structura\Console\Commands\ActionCreation;
use Structura\Console\Commands\ServiceCreation;

class ConsoleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ActionCreation::class,
                CacheCreation::class,
                ServiceCreation::class,
            ]);
        }
    }
}
