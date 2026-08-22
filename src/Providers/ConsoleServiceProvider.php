<?php

namespace KaueF\Structura\Providers;

use Illuminate\Support\ServiceProvider;
use KaueF\Structura\Console\Commands\ActionCreationCommand;
use KaueF\Structura\Console\Commands\CacheCreationCommand;
use KaueF\Structura\Console\Commands\DataCreationCommand;
use KaueF\Structura\Console\Commands\EnumCreationCommand;
use KaueF\Structura\Console\Commands\HelperCreationCommand;
use KaueF\Structura\Console\Commands\ServiceCreationCommand;
use KaueF\Structura\Console\Commands\StructuraInstallCommand;
use KaueF\Structura\Console\Commands\TraitCreationCommand;

class ConsoleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/structura.php' => config_path('structura.php'),
        ], 'structura.config');

        $this->publishes([
            __DIR__.'/../../resources/boost' => base_path('resources/boost'),
        ], 'boost-skills');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ActionCreationCommand::class,
                CacheCreationCommand::class,
                DataCreationCommand::class,
                EnumCreationCommand::class,
                HelperCreationCommand::class,
                ServiceCreationCommand::class,
                StructuraInstallCommand::class,
                TraitCreationCommand::class,
            ]);
        }
    }
}
