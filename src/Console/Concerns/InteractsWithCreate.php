<?php

namespace KaueF\Structura\Console\Concerns;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

trait InteractsWithCreate
{
    abstract protected function namespaceRoot(): string;
    abstract protected function type(): string;

    /**
     * Get the fully qualified class name.
     *
     * @param string $name
     * @return string
     */
    protected function getClassName(string $name): string
    {
        $name = trim($name);

        if (empty($name)) {
            $this->error("\n❌ {$this->formatType()} name cannot be empty.\n");
            exit(1);
        }

        $this->validateName($name);

        $parts = preg_split('/[\/\\\\]/', $name);

        $className = ucfirst(array_pop($parts));

        if (! str_ends_with(strtolower($className), strtolower($this->type())))
            $className .= $this->formatType();

        $parts = array_map('ucfirst', $parts);
        $parts[] = $className;

        return implode('/', $parts);
    }

    /**
     * Validate name.
     * 
     * @param string $name
     * @return void
     */
    protected function validateName(string $name): void
    {
        if (!preg_match('/^([a-zA-Z]+[\/\\\\]?)+$/', $name)) {
            $this->error("\n❌ Invalid name. Only alphabetic characters and namespace separators ('/' or '\\') are allowed.\n");
            exit(1);
        }
    }

    /**
     * Get the file path.
     * 
     * @param string $name
     * @return string
     */
    protected function getPath($name): string
    {
        $path = match (strtolower($this->type())) {
            'action' => app_path("Actions/{$name}.php"),
            'cache' => app_path("Caches/{$name}.php"),
            'enum' => app_path("Enums/{$name}.php"),
            'helper' => app_path("Helpers/{$name}.php"),
            'service' => app_path("Services/{$name}.php"),
            default => $this->error("\n⚠️ Unable to resolve the file path. Invalid type: {$this->type()}. \n")
        };

        if (empty($path))
            return exit(1);

        if (File::exists($path)) {
            $this->warn("\n⚠️  {$this->formatType()} already exists!\n");
            exit(1);
        }

        return $path;
    }

    /**
     * Get the namespace.
     * 
     * @param string $name
     * @return string
     */
    protected function getNamespace(string $name): string
    {
        $directory = dirname($name);
        return $this->namespaceRoot() . ($directory === '.' ? '' : '\\' . str_replace('/', '\\', $directory));
    }

    /**
     * Finish creation.
     * 
     * @param string $path
     * @param string $content
     * @return void
     */
    protected function finishCreation(string $path, string $content): void
    {
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("\n✨ {$this->formatType()} created successfully!");
        $this->line("📝 [{$path}] \n");
    }

    /**
     * Format type.
     * 
     * @return string
     */
    protected function formatType(): string
    {
        return ucfirst(strtolower($this->type()));
    }

    /**
     * Run composer dump-autoload
     * 
     * @return void
     */
    protected function runDumpAutoload(): void
    {
        (app()->environment('production'))
            ? $this->comment("\n" . 'ℹ️ Run "composer dump-autoload" if needed.')
            : Process::run('composer dump-autoload')->output();
    }
}
