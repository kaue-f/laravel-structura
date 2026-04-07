<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class HelperCreationCommand extends GeneratorCommand
{
    use InteractsWithCreate;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structura:helper 
                            {name? : Helper name}
                            {--e|example : Add example method to helper}
                            {--g|global : Create a global helper registered in composer}
                            {--s|stub : Create helper from package stub}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new helper class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Helper';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../../stubs/helper.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return config('structura.namespaces.helper', $rootNamespace.'\Helpers');
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $name = trim($this->argument('name'));
        if (! $name) {
            return $name;
        }

        return Str::finish(
            $name,
            config('structura.suffixes.helper', 'Helper')
        );
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

        $use_stub = $this->optionOrConfig('helper', 'stub');
        $use_global = $this->optionOrConfig('helper', 'global');

        if ($use_stub && ! $this->argument('name')) {
            return $this->createHelperStub();
        }

        if (! $use_stub && ! $this->argument('name')) {
            $this->error("\n❌ Helper name is required.\n");

            return self::FAILURE;
        }

        if ($this->argument('name') && ! $use_stub) {
            return ($use_global)
                ? $this->createGlobalHelper()
                : parent::handle();
        }

        return self::SUCCESS;
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
        $is_example = $this->optionOrConfig('helper', 'example');

        return str_replace(
            ['{{example}}'],
            [($is_example) ? $this->exampleMethod() : '//'],
            $stub
        );
    }

    /**
     * Create a helper from package stub.
     */
    protected function createHelperStub(): int
    {
        $path = app_path('Helpers/helpers.php');

        if (file_exists($path)) {
            $this->warn("\n⚠️ Helper from package stub already exists!\n");

            return self::FAILURE;
        }

        File::ensureDirectoryExists(dirname($path));
        copy(
            __DIR__.'/../../Helpers/helpers.php',
            $path
        );

        if (! file_exists($path)) {
            $this->error("\n❌ Failed to create helper from package stub.\n");

            return self::FAILURE;
        }

        $this->registerComposer('helpers.php');
        $this->info("\n✨ Helper from package stub created successfully!");
        $this->line("📝 [{$path}] \n");

        return self::SUCCESS;
    }

    /**
     * Create a global helper.
     */
    protected function createGlobalHelper(): int
    {
        $qualifiedName = $this->qualifyClass($this->getNameInput());

        $basename = class_basename($qualifiedName);
        $snakeName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $basename));

        $qualifiedName = str_replace($basename, $snakeName, $qualifiedName);

        $path = $this->getPath($qualifiedName);

        if (File::exists($path)) {
            $this->warn("\n⚠️  {$this->type} already exists!\n");

            return self::FAILURE;
        }

        $this->makeDirectory($path);
        File::put($path, $this->getPHP());

        $this->registerComposer("{$snakeName}.php");
        $this->info("\n✨ Helper global created successfully!");
        $this->line("📝 [{$path}] \n");

        return self::SUCCESS;
    }

    /**
     * Validate the method options.
     */
    protected function validateMethodOptions(): bool
    {
        $methods = collect(['example', 'stub', 'global'])
            ->filter(fn ($option) => $this->option($option));

        if ($methods->count() > 1) {
            $this->error("\n⚠️ Choose only one method option: --example, --stub or --global.\n");

            return false;
        }

        return true;
    }

    /**
     * Get the example method stub.
     */
    protected function exampleMethod(): string
    {
        return <<<'PHP'
    /**
         * Example helper method.
         * 
         * @param mixed $value
         * @return mixed
         */
        public static function example(mixed $value): mixed
        {
            return $value;
        }
    PHP;
    }

    /**
     * Get the PHP stub.
     */
    protected function getPHP(): string
    {
        return <<<'PHP'
    <?php
    
    
    PHP;
    }

    /**
     * Register the helper in composer.json
     * Run composer dump-autoload in process end, when not production.
     */
    protected function registerComposer(string $helper): void
    {
        $composer_path = base_path('composer.json');
        $composer = json_decode(File::get($composer_path), true);

        $files = $composer['autoload']['files'] ?? [];

        $helper_path = "app/Helpers/{$helper}";

        if (! in_array($helper_path, $files, true)) {
            $files[] = $helper_path;
        }

        sort($files, SORT_STRING | SORT_FLAG_CASE);
        $composer['autoload']['files'] = $files;

        File::put(
            $composer_path,
            json_encode($composer, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES)
        );

        $this->runDumpAutoload();
    }
}
