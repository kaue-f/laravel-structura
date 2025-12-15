<?php

namespace Structura\Commands;

use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ServiceCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}
                            {--construct : Create an service with __construct method (default)}
                            {--raw :  Create an service with without method}';

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
    protected string $namespaceRoot = 'App\\Services';

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle()
    {
        $this->validateMethodOptions();
        $this->info("\n🚀 Creating new service...\n");

        $name = $this->getClassName();
        $path = $this->getPath($name);

        if (File::exists($path)) {
            $this->error("\n❌ The service already exists!\n");
            return self::FAILURE;
        }

        $stub = file_get_contents(__DIR__ . '/../../Stubs/service.stub');

        $content = str_replace(
            ['{{namespace}}', '{{class}}', '{{method}}'],
            [
                $this->getNamespace($name),
                class_basename($name),
                $this->getMethodStub()
            ],
            $stub
        );

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("\n✨ Service created successfully!\n");
        return self::SUCCESS;
    }

    /**
     * Get the fully qualified class name.
     * 
     * return string
     */
    protected function getClassName(): string
    {
        $name = trim($this->argument('name'));

        if (empty($name))
            throw new InvalidArgumentException("\nService name cannot be empty");

        $this->validateName($name);

        $parts = preg_split('/[\/\\\\]/', $name);

        $className = ucfirst(array_pop($parts));

        if (!str_ends_with(strtolower($className), 'service'))
            $className .= 'Service';

        $parts = array_map('ucfirst', $parts);

        $parts[] = $className;
        return implode('/', $parts);
    }

    /**
     * Validate the service name.
     * 
     * @param string $name
     * @return void
     */
    protected function validateName(string $name): void
    {
        if (!preg_match('/^([a-zA-Z]+[\/\\\\]?)+$/', $name))
            throw new InvalidArgumentException(
                "Invalid service name. Only alphabetic characters and namespace separators ('/' or '\\') are allowed."
            );
    }

    /**
     * Get the file path for the service.
     * 
     * @param string $name
     * @return string
     */
    protected function getPath(string $name): string
    {
        return app_path("Services/{$name}.php");
    }

    /**
     * Get the namespace for the service.
     * 
     * @param string $name
     * @return string
     */
    protected function getNamespace(string $name): string
    {
        $directory = dirname($name);

        return $this->namespaceRoot . ($directory === '.' ? '' : '\\' . str_replace('/', '\\', $directory));
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

        if ($methods->count() > 1)
            throw new InvalidArgumentException(
                "⚠️ Choose only one option: --construct or --raw."
            );
    }

    /**
     * Get the method stub based on the selected option.
     * 
     * @return string
     */
    protected function getMethodStub(): string
    {
        return match (true) {
            $this->option('raw') => '',
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
