<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class HelperCreation extends Command
{
    use InteractsWithCreate;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structura:helper 
                            {name? : Helper name}
                            {--e|example : Add example method to helper (default)}
                            {--g|global : Create a global helper registered in composer}
                            {--s|stub : Create helper from package stub}
                            {--r|raw : Create a standalone helper without methods}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new action class';

    /**
     * The root namespace for actions.
     *
     * @var string
     */
    protected function namespaceRoot(): string
    {
        return 'App\\Helpers';
    }

    /**
     * The type of the console command.
     *
     * @var string
     */
    protected function type(): string
    {
        return 'helper';
    }

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle()
    {
        $this->validateMethodOptions();
        $this->info("🚀 Creating new helper...");

        if ($this->option('stub') && !$this->argument('name'))
            $this->createHelperStub();

        if (!$this->option('stub') && !$this->argument('name')) {
            $this->error("\n❌ Helper name is required.\n");
            return self::FAILURE;
        }

        if ($this->argument('name') && !$this->option('stub')) {
            ($this->option('global'))
                ? $this->createGlobalHelper()
                : $this->createHelper();
        }

        return self::SUCCESS;
    }

    /**
     * Create a helper from package stub.
     * 
     * @return void
     */
    protected function createHelperStub()
    {
        $path = app_path("Helpers/helpers.php");

        if (file_exists(($path))) {
            $this->warn("\n⚠️ Helper from package stub already exists!\n");
            exit(self::FAILURE);
        }

        File::ensureDirectoryExists(dirname($path));
        copy(
            __DIR__ . '/../../Helpers/helpers.php',
            $path
        );

        if (!file_exists($path)) {
            $this->error("\n❌ Failed to create helper from package stub.\n");
            exit(self::FAILURE);
        }

        $this->registerComposer('helpers.php');
        $this->info("\n✨ Helper from package stub created successfully!");
        $this->line("📝 [{$path}] \n");
    }

    /**
     * Create a global helper.
     * 
     * @return void
     */
    protected function createGlobalHelper()
    {
        $name = $this->getClassName($this->argument('name'));
        $name = str_ireplace(
            class_basename($name),
            strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', class_basename($name))),
            $name
        );
        $path = $this->getPath($name);

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $this->getPHP());

        $this->registerComposer("{$name}.php");
        $this->info("\n✨ Helper global created successfully!");
        $this->line("📝 [{$path}] \n");
    }

    /**
     * Create a class of helper.
     * 
     * @return void
     */
    protected function createHelper()
    {
        $name = $this->getClassName($this->argument('name'));
        $path = $this->getPath($name);
        $stub = file_get_contents(__DIR__ . '/../../../stubs/helper.stub');

        $content = str_replace(
            ['{{namespace}}', '{{class}}', '{{exemple}}'],
            [
                $this->getNamespace($name),
                class_basename($name),
                ($this->option('raw')) ? '//' : $this->exampleMethod(),
            ],
            $stub
        );

        $this->finishCreation($path, $content);
    }

    /**
     * Validate the method options.
     * 
     * @return void
     */
    protected function validateMethodOptions(): void
    {
        $methods = collect(['example', 'stub', 'global', 'raw'])
            ->filter(fn($option) => $this->option($option));

        if ($methods->count() > 1) {
            $this->error("\n⚠️ Choose only one method option: --example, --stub, --global or raw.\n");
            exit(self::FAILURE);
        }
    }

    /**
     * Get the example method stub.
     * 
     * @return string
     */
    protected function exampleMethod(): string
    {
        return <<<PHP
    /**
         * Example helper method.
         * 
         * @param mixed \$value
         * @return mixed
         */
        public static function example(mixed \$value): mixed
        {
            return \$value;
        }
    PHP;
    }

    /**
     * Get the PHP stub.
     * 
     * @return string
     */
    protected function getPHP(): string
    {
        return <<<PHP
    <?php
    
    
    PHP;
    }

    /**
     * Register the helper in composer.json
     * Run composer dump-autoload in process end, when not production.
     * 
     * @param string $helper
     * @return void
     */
    protected function registerComposer(string $helper): void
    {
        $composer_path = base_path('composer.json');
        $composer = json_decode(File::get($composer_path), true);

        $files = $composer['autoload']['files'] ?? [];

        $helper_path = "app/Helpers/{$helper}";

        if (! in_array($helper_path, $files, true))
            $files[] = $helper_path;

        sort($files, SORT_STRING | SORT_FLAG_CASE);
        $composer['autoload']['files'] = $files;

        File::put(
            $composer_path,
            json_encode($composer, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES)
        );

        $this->runDumpAutoload();
    }
}
