<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Console\Command;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class ServiceCreation extends Command
{
    use InteractsWithCreate;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structura:service {name : Service name}
                            {--c|construct : Create an service with a __construct method (default)}
                            {--r|raw :  Create an service with without method}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * The root namespace for services.
     *
     * @var string
     */
    protected function namespaceRoot(): string
    {
        return 'App\\Services';
    }

    /**
     * The type of the console command.
     *
     * @var string
     */
    protected function type(): string
    {
        return 'service';
    }

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle()
    {
        $this->validateMethodOptions();
        $this->info("🚀 Creating new service...");

        $name = $this->getClassName($this->argument('name'));
        $path = $this->getPath($name);
        $stub = file_get_contents(__DIR__ . '/../../../stubs/service.stub');

        $content = str_replace(
            ['{{namespace}}', '{{class}}', '{{method}}'],
            [
                $this->getNamespace($name),
                class_basename($name),
                $this->getMethodStub()
            ],
            $stub
        );

        $this->finishCreation($path, $content);
        return self::SUCCESS;
    }

    /**
     * Validate the method options.
     * 
     * @return void
     */
    protected function validateMethodOptions(): void
    {
        $methods = collect(['construct', 'raw'])
            ->filter(fn($option) => $this->option($option));

        if ($methods->count() > 1) {
            $this->error("⚠️ Choose only one option: --construct or --raw.");
            exit(self::FAILURE);
        }
    }

    /**
     * Get the method stub based on the selected option.
     * 
     * @return string
     */
    protected function getMethodStub(): string
    {
        return match (true) {
            $this->option('raw') => '//',
            default => $this->constructMethod(),
        };
    }

    /**
     * Get the construct method stub.
     * 
     * @return string
     */
    protected function constructMethod(): string
    {
        return <<<PHP
    public function __construct()
        {
            //
        }
    PHP;
    }
}
