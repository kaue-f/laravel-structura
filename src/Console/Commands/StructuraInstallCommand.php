<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Console\Command;

class StructuraInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structura:install {--force : Overwrite config file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new DTO';

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle(): int
    {
        if (file_exists(config_path('structura.php')) && ! $this->option('force')) {
            $this->warn("\nConfig structura.php already exists.\n");
            return self::SUCCESS;
        }

        $this->callSilent('vendor:publish', [
            '--tag' => 'structura.config',
            '--force' => $this->option('force'),
        ]);

        $this->info("\n✨ Structura config published successfully.");

        return self::SUCCESS;
    }
}
