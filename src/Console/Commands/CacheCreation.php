<?php

namespace Structura\Console\Commands;

use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CacheCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:cache {name}
                            {--base : Create a cache class extending CacheService (default)}
                            {--raw : Create a standalone cache class without CacheService extensio}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new cache class (extends CacheService by default)';

    /**
     * The root namespace for actions.
     *
     * @var string
     */
    protected $namespaceRoot = 'App\\Services\\Caches';

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle()
    {
        $this->validateMethodOptions();
        $this->info("🚀 Creating new cache...");

        $name = $this->getClassName();
        $path = $this->getPath($name);

        if (File::exists($path)) {
            $this->error("\n❌ The cache already exists!");
            return self::FAILURE;
        }

        $stub = file_get_contents(filename: __DIR__ . '/../../../Stubs/cache.stub');

        $content = str_replace(
            ['{{namespace}}', '{{class}}', '{{extends}}', '{{imports}}', '{{prefix}}'],
            [
                $this->getNamespace($name),
                class_basename($name),
                ($this->option('raw')) ? '' : 'extends CacheService',
                ($this->option('raw')) ? '' : $this->getImportsStub(),
                $this->getPrefixStub($name)
            ],
            $stub
        );

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("\n✨ Cache created successfully!");
        $this->line("📝 [{$path}] \n");
        return self::SUCCESS;
    }

    /**
     * Get the fully qualified class name.
     * 
     * @return string
     */
    protected function getClassName(): string
    {
        $name = trim($this->argument('name'));

        if (empty($name))
            throw new InvalidArgumentException("\n⚠️ Cache name cannot be empty");

        $this->validateName($name);

        $parts = preg_split('/[\/\\\\]/', $name);

        $className = ucfirst(array_pop($parts));

        if (!str_ends_with(strtolower($className), 'cache'))
            $className .= 'Cache';

        $parts = array_map('ucfirst', $parts);

        $parts[] = $className;
        return implode('/', $parts);
    }

    /**
     * Validate the cache name.
     * 
     * @param string $name
     * @return void
     */
    protected function validateName(string $name): void
    {
        if (!preg_match('/^([a-zA-Z]+[\/\\\\]?)+$/', $name))
            throw new InvalidArgumentException(
                "⚠️ Invalid cache name. Only alphabetic characters and namespace separators ('/' or '\\') are allowed."
            );
    }

    /**
     * Get the file path for the cache.
     * 
     * @param string $name
     * @return string
     */
    protected function getPath(string $name): string
    {
        return app_path("Services/Caches/$name.php");
    }

    /**
     * Get the namespace for the cache.
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
        $methods = collect(['base', 'raw'])
            ->filter(fn($option) => $this->option($option));

        if ($methods->count() > 1)
            throw new InvalidArgumentException(
                "⚠️ Choose only one option: --base or --raw."
            );
    }

    /**
     * Get the imports stub based on the selected option.
     * 
     * @return string
     */
    protected function getImportsStub(): string
    {
        return <<<PHP

    use Structura\Support\Cache\CacheService;

    PHP;
    }

    /**
     * Get the prefix stub. 
     * 
     * @param string $name
     * @return string
     */
    protected function getPrefixStub(string $name): string
    {
        $name = strtolower(preg_replace('/Cache$/', '', $name));

        return <<<PHP
    /**
         * Name of the cache key.
         * 
         * @var string
         */
        protected string \$prefix = '$name';
    PHP;
    }
}
