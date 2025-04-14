<?php

namespace Structura;

use Structura\Commands\CacheCreation;
use Structura\Commands\ActionCreation;
use Illuminate\Support\ServiceProvider;
use Structura\Commands\ServiceCreation;

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
