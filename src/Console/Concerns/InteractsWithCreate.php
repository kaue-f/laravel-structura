<?php

namespace KaueF\Structura\Console\Concerns;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

trait InteractsWithCreate
{
    protected $ds = DIRECTORY_SEPARATOR;
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
        $type = strtolower($this->type());

        $path_default = match ($type) {
            'action' => app_path('Actions'),
            'cache' => app_path('Caches'),
            'dto' => app_path('DTOs'),
            'enum' => app_path('Enums'),
            'helper' => app_path('Helpers'),
            'service' => app_path('Services'),
            'trait' => app_path('Concerns'),
            default => $this->error("\n⚠️ Unable to resolve the file path. Invalid type: {$this->type()}. \n")
        };

        if (empty($path_default))
            return exit(1);

        $path = config("structura.paths.{$type}", $path_default);
        $path .= "{$this->ds}{$name}.php";

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

    protected function optionOrConfig(string $key, string $option, bool $default = false): bool
    {
        if ($this->option($option))
            return true;

        if (config()->has("structura.default_optins.{$key}.{$option}"))
            return (bool) config("structura.default_optins.{$key}.{$option}", $default);

        return $default;
    }

    protected function optionValueOrConfig(string $key, string $option, ?string $default = null): ?string
    {
        $value = $this->option($option);

        if ($value !== null)
            return $value;

        if (config()->has("structura.default_optins.{$key}.{$option}"))
            return config("structura.default_optins.{$key}.{$option}", $default);

        return $default;
    }
}
