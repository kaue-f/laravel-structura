<?php

namespace KaueF\Structura\Providers;

use Illuminate\Support\ServiceProvider;
use KaueF\Structura\Console\Commands\CacheCreation;
use KaueF\Structura\Console\Commands\ActionCreation;
use KaueF\Structura\Console\Commands\HelperCreation;
use KaueF\Structura\Console\Commands\ServiceCreation;

class ConsoleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ActionCreation::class,
                CacheCreation::class,
                HelperCreation::class,
                ServiceCreation::class,
            ]);
        }
    }
}
