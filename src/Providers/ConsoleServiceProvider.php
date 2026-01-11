<?php

namespace KaueF\Structura\Providers;

use Illuminate\Support\ServiceProvider;
use KaueF\Structura\Console\Commands\DTOCreationCommand;
use KaueF\Structura\Console\Commands\EnumCreationCommand;
use KaueF\Structura\Console\Commands\CacheCreationCommand;
use KaueF\Structura\Console\Commands\TraitCreationCommand;
use KaueF\Structura\Console\Commands\ActionCreationCommand;
use KaueF\Structura\Console\Commands\HelperCreationCommand;
use KaueF\Structura\Console\Commands\ServiceCreationCommand;
use KaueF\Structura\Console\Commands\StructuraInstallCommand;

class ConsoleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/structura.php' => config_path('structura.php'),
        ], 'structura.config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ActionCreationCommand::class,
                CacheCreationCommand::class,
                DTOCreationCommand::class,
                EnumCreationCommand::class,
                HelperCreationCommand::class,
                ServiceCreationCommand::class,
                StructuraInstallCommand::class,
                TraitCreationCommand::class,
            ]);
        }
    }
}
