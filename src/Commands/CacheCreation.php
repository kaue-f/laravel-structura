<?php

namespace Structura\Commands;

use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputArgument;

class CacheCreation extends Command
{
    protected $signature = 'make:cache {name}
                            {--base : Create a cache class extending BaseCache (default)}
                            {--raw : Create a standalone cache class without BaseCache extensio}';

    protected $description = 'Create a new cache class (extends BaseCache by default)';

    protected $baseCachePath = 'Services/Caches/BaseCache.php';

    public function handle()
    {
        $name = $this->getClassName();
        $path = $this->getPath($name);

        if (!File::exists(app_path($this->baseCachePath)) && !$this->option('raw'))
            $this->createBaseCache();

        $stub = $this->getStub();
        $namespace = $this->getNamespace($name);
        $className = basename($name);

        if (File::exists($path)) {
            $this->error("\nThe cache {$className} already exists!\n");
            return Command::FAILUR;
        }

        $content = str_replace(
            ['{{namespace}}', '{{class}}'],
            [$namespace, $className],
            $stub
        );

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("\n✅ Cache {$className} created successfully!\n");
        return Command::SUCCESS;
    }

    protected function createBaseCache(): void
    {
        $path = app_path($this->baseCachePath);
        $template = file_get_contents(dirname(__DIR__) . '/../Stubs/Caches/baseCache.stub');

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $template);
    }

    protected function getClassName(): string
    {
        $name = trim($this->argument('name'));

        if (empty($name))
            throw new InvalidArgumentException("\nCache name cannot be empty");

        $this->validateName($name);

        $parts = preg_split('/[\/\\\\]/', $name);

        $className = ucfirst(array_pop($parts));

        if (!str_ends_with(strtolower($className), 'cache'))
            $className .= 'Cache';

        $parts = array_map('ucfirst', $parts);

        $parts[] = $className;
        return implode('/', $parts);
    }

    protected function validateName(string $name): void
    {
        if (!preg_match('/^([a-zA-Z]+[\/\\\\]?)+$/', $name))
            throw new InvalidArgumentException(
                "Invalid cache name. Only alphabetic characters and namespace separators ('/' or '\\') are allowed."
            );
    }

    protected function getPath(string $name): string
    {
        return app_path("Services/Caches/$name.php");
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
            ['name', InputArgument::REQUIRED, 'The name of the cache']
        ];
    }

    protected function getStub(): string
    {
        return match (true) {
            $this->option('raw') => file_get_contents(dirname(__DIR__) . '/../Stubs/Caches/raw.stub'),
            default => file_get_contents(dirname(__DIR__) . '/../Stubs/Caches/base.stub'),
        };
    }
}
