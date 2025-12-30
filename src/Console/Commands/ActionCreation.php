<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Console\Command;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class ActionCreation extends Command
{
    use InteractsWithCreate;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structura:action {name : Action name}
                            {--c|construct : Create an action with a __construct method}  
                            {--e|execute : Create an action with a execute method (default)}
                            {--l|handle : Create an action with a handle method}
                            {--i|invokable : Create an action with a __invoke method}
                            {--r|raw : Create an action without methods}';

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
        return 'App\\Actions';
    }

    /**
     * The type of the console command.
     *
     * @var string
     */
    protected function type(): string
    {
        return 'action';
    }

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle()
    {
        $this->validateMethodOptions();
        $this->info("🚀 Creating new action...");

        $name = $this->getClassName($this->argument('name'));
        $path = $this->getPath($name);
        $stub = file_get_contents(__DIR__ . '/../../../stubs/action.stub');

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
        $methods = collect(['execute', 'handle', 'invokable', 'raw'])
            ->filter(fn($option) => $this->option($option));

        if ($methods->count() > 1) {
            $this->error("\n⚠️ Choose only one method option: --execute, --handle, --invokable or --raw.\n");
            exit(self::FAILURE);
        }
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
            $this->option('raw') => '//',
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
