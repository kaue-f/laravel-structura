<?php

namespace Structura\Commands;

use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputArgument;

class ServiceCreation extends Command
{
    protected $signature = 'make:service {name}';

    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->getClassName();
        $path = $this->getPath($name);

        $stub = $this->getStub();
        $namespace = $this->getNamespace($name);
        $className = basename($name);

        if (File::exists($path)) {
            $this->error("\nThe service {$className} already exists!\n");
            return Command::FAILURE;
        }

        $content = str_replace(
            ['{{namespace}}', '{{class}}'],
            [$namespace, $className],
            $stub
        );

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("\n✅ Service {$className} created successfully!\n");
        return Command::SUCCESS;
    }

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

    protected function validateName(string $name): void
    {
        if (!preg_match('/^([a-zA-Z]+[\/\\\\]?)+$/', $name))
            throw new InvalidArgumentException(
                "Invalid service name. Only alphabetic characters and namespace separators ('/' or '\\') are allowed."
            );
    }

    protected function getPath(string $name): string
    {
        return app_path("Services/{$name}.php");
    }

    protected function getNamespace(string $name): string
    {
        $directory = dirname($name);

        return ($directory === '.')
            ? ''
            : '\\' . str_replace('/', '\\', $directory);
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, "\nThe name of the service\n"]
        ];
    }

    protected function getStub(): string
    {
        return file_get_contents(dirname(__DIR__) . '/../Stubs/Services/raw.stub');
    }
}
