<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class CacheCreationCommand extends GeneratorCommand
{
    use InteractsWithCreate;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structura:cache {name : Cache name}
                            {--e|extend : Create a cache class extending CacheSupport}
                            {--r|raw : Create a standalone cache class without CacheSupport extension}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new cache class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Cache';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../../stubs/cache.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return config('structura.namespaces.cache', $rootNamespace.'\Caches');
    }

    /**
     * Execute the console command.
     *
     * @return int|bool|null
     */
    public function handle()
    {
        if ($this->validateMethodOptions() === false) {
            return self::FAILURE;
        }

        return parent::handle();
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $is_raw = $this->optionOrConfig('cache', 'raw');
        $use_extend = $this->optionOrConfig('cache', 'extend');

        return str_replace(
            ['{{extends}}', '{{imports}}', '{{prefix}}'],
            [
                (! $is_raw && $use_extend) ? 'extends CacheSupport' : '',
                (! $is_raw && $use_extend) ? $this->getImportsStub() : '',
                ($is_raw) ? '//' : $this->getPrefixStub($name),
            ],
            $stub
        );
    }

    /**
     * Validate the method options.
     */
    protected function validateMethodOptions(): bool
    {
        $methods = collect(['extend', 'raw'])
            ->filter(fn ($option) => $this->option($option));

        if ($methods->count() > 1) {
            $this->error('⚠️ Choose only one option: --extend or --raw.');

            return false;
        }

        return true;
    }

    /**
     * Get the imports stub based on the selected option.
     */
    protected function getImportsStub(): string
    {
        return <<<PHP

    use KaueF\Structura\Support\CacheSupport;

    PHP;
    }

    /**
     * Get the prefix stub.
     */
    protected function getPrefixStub(string $name): string
    {
        $name = strtolower(preg_replace('/Cache$/', '', class_basename($name)));

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
