<?php

namespace Structura\Commands;

use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputArgument;

class ActionCreation extends Command
{
    protected $signature = 'make:action {name}
                            {--execute : Create an action with execute method (default)}
                            {--invoke : Create an action with __invoke method}
                            {--raw :  Create an action with without method}';

    protected $description = 'Create a new action class';

    public function handle()
    {
        $name = $this->getClassName();
        $path = $this->getPath($name);

        $stub = $this->getStub();
        $namespace = $this->getNamespace($name);
        $className = basename($name);

        if (File::exists($path)) {
            $this->error("\nThe action {$className} already exists!\n");
            return Command::FAILURE;
        }

        $content = str_replace(
            ['{{namespace}}', '{{class}}'],
            [$namespace, $className],
            $stub
        );

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("\n✅ Action {$className} created successfully!\n");
        return Command::SUCCESS;
    }

    protected function getClassName(): string
    {
        $name = trim($this->argument('name'));

        if (empty($name))
            throw new InvalidArgumentException("\nAction name cannot be empty.\n");

        $this->validateName($name);

        $parts = preg_split('/[\/\\\\]/', $name);

        $className = ucfirst(array_pop($parts));

        if (!str_ends_with(strtolower($className), 'action'))
            $className .= 'Action';

        $parts = array_map('ucfirst', $parts);

        $parts[] = $className;
        return implode('/', $parts);
    }

    protected function validateName(string $name): void
    {
        if (!preg_match('/^([a-zA-Z]+[\/\\\\]?)+$/', $name))
            throw new InvalidArgumentException(
                "Invalid action name. Only alphabetic characters and namespace separators ('/' or '\\') are allowed."
            );
    }

    protected function getPath(string $name): string
    {
        return app_path("Actions/$name.php");
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
            ['name', InputArgument::REQUIRED, "\nThe name of the action.\n"]
        ];
    }

    protected function getStub(): string
    {
        return match (true) {
            $this->option('invoke') => file_get_contents(dirname(__DIR__) . '/../Stubs/Actions/invoke.stub'),
            $this->option('raw') => file_get_contents(dirname(__DIR__) . '/../Stubs/Actions/raw.stub'),
            default => file_get_contents(dirname(__DIR__) . '/../Stubs/Actions/execute.stub'),
        };
    }
}
