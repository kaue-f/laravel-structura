<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Console\Command;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class CacheCreation extends Command
{
    use InteractsWithCreate;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structura:cache {name : Cache name}
                            {--e|extend : Create a cache class extending CacheSupport}
                            {--r|raw : Create a standalone cache class without CacheSupport extensio}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new cache class';

    /**
     * The root namespace for actions.
     *
     * @var string
     */
    protected function namespaceRoot(): string
    {
        return 'App\\Caches';
    }

    /**
     * The type of the console command.
     *
     * @var string
     */
    protected function type(): string
    {
        return 'cache';
    }

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle()
    {
        $this->validateMethodOptions();
        $this->info("🚀 Creating new cache...");

        $name = $this->getClassName($this->argument('name'));
        $path = $this->getPath($name);
        $stub = file_get_contents(filename: __DIR__ . '/../../../stubs/cache.stub');

        $content = str_replace(
            ['{{namespace}}', '{{class}}', '{{extends}}', '{{imports}}', '{{prefix}}'],
            [
                $this->getNamespace($name),
                class_basename($name),
                ($this->option('extend')) ? 'extends CacheSupport' : '',
                ($this->option('extend')) ?  $this->getImportsStub() : '',
                ($this->option('raw')) ? '//' : $this->getPrefixStub($name)
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
        $methods = collect(['extend', 'raw'])
            ->filter(fn($option) => $this->option($option));

        if ($methods->count() > 1) {
            $this->error("⚠️ Choose only one option: --extend or --raw.");
            exit(self::FAILURE);
        }
    }

    /**
     * Get the imports stub based on the selected option.
     * 
     * @return string
     */
    protected function getImportsStub(): string
    {
        return <<<PHP

    use KaueF\Structura\Support\Cache\CacheSupport;

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
