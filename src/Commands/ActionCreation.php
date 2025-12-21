<?php

namespace Structura\Commands;

use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ActionCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:action {name}
                            {--construct : Create an action with a __construct method}  
                            {--execute : Create an action with a execute method (default)}
                            {--handle : Create an action with a handle method}
                            {--invokable : Create an action with a __invoke method}
                            {--raw : Create an action without methods}';

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
    protected string $namespaceRoot = 'App\\Actions';

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle()
    {
        $this->validateMethodOptions();
        $this->info("\n🚀 Creating new action...\n");

        $name = $this->getClassName();
        $path = $this->getPath($name);

        if (File::exists($path)) {
            $this->error("\n❌ Action already exists!\n");
            return self::FAILURE;
        }

        $stub = file_get_contents(__DIR__ . '/../../Stubs/action.stub');

        $content = str_replace(
            ['{{namespace}}', '{{class}}', '{{method}}', '{{constructor}}'],
            [
                $this->getNamespace($name),
                class_basename($name),
                $this->getMethodStub(),
                $this->option('construct') ? $this->constructMethod() : ''
            ],
            $stub
        );

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("\n✨ Action created successfully!\n");
        return self::SUCCESS;
    }

    /**
     * Get the fully qualified class name.
     * 
     * return string
     */
    protected function getClassName(): string
    {
        $name = trim($this->argument('name'));

        if (empty($name))
            throw new InvalidArgumentException("\n⚠️ Action name cannot be empty.\n");

        $this->validateName($name);

        $parts = preg_split('/[\/\\\\]/', $name);

        $className = ucfirst(array_pop($parts));

        if (!str_ends_with(strtolower($className), 'action'))
            $className .= 'Action';

        $parts = array_map('ucfirst', $parts);

        $parts[] = $className;
        return implode('/', $parts);
    }

    /**
     * Validate the action name.
     * 
     * @param string $name
     * @return void
     */
    protected function validateName(string $name): void
    {
        if (!preg_match('/^([a-zA-Z]+[\/\\\\]?)+$/', $name))
            throw new InvalidArgumentException(
                "⚠️ Invalid action name. Only alphabetic characters and namespace separators ('/' or '\\') are allowed."
            );
    }

    /**
     * Get the file path for the action.
     * 
     * @param string $name
     * @return string
     */
    protected function getPath(string $name): string
    {
        return app_path("Actions/$name.php");
    }

    /**
     * Get the namespace for the action.
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
        $methods = collect(['execute', 'handle', 'invokable', 'raw'])
            ->filter(fn($option) => $this->option($option));

        if ($methods->count() > 1)
            throw new InvalidArgumentException(
                "⚠️ Choose only one method option: --execute, --handle, --invokable or --raw."
            );
    }

    /**
     * Get the method stub based on the selected option.
     * 
     * @return string
     */
    protected function getMethodStub(): string
    {
        return match (true) {
            $this->option('handle') => $this->handleMethod(),
            $this->option('invokable') => $this->invokableMethod(),
            $this->option('raw') => '',
            default => $this->executeMethod(),
        };
    }

    /**
     * Get the execute method stub.
     * 
     * @return string
     */
    protected function executeMethod(): string
    {
        return <<<PHP
    public function execute()
        {
            //
        }
    PHP;
    }

    /**
     * Get the handle method stub.
     * 
     * @return string
     */
    protected function handleMethod(): string
    {
        return <<<PHP
    public function handle()
        {
            //
        }
    PHP;
    }

    /**
     * Get the invokable method stub.
     * 
     * @return string
     */
    protected function invokableMethod(): string
    {
        return <<<PHP
    public function __invoke()
        {
            //
        }
    PHP;
    }

    /**
     * Get the construct method stub.
     * 
     * @return string
     */
    protected function constructMethod(): string
    {
        return <<<PHP
    public function __construct()
        {
            //
        }

        
    PHP;
    }
}
