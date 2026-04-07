<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class ActionCreationCommand extends GeneratorCommand
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
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Action';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../../stubs/action.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return config('structura.namespaces.action', $rootNamespace.'\Actions');
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return Str::finish(
            trim($this->argument('name')),
            config('structura.suffixes.action', 'Action')
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

        $is_raw = $this->optionOrConfig('action', 'raw');
        $use_construct = $this->optionOrConfig('action', 'construct');

        return str_replace(
            ['{{method}}', '{{constructor}}'],
            [
                $this->getMethodStub($is_raw),
                (! $is_raw && $use_construct) ? $this->constructMethod() : '',
            ],
            $stub
        );
    }

    /**
     * Validate the method options.
     */
    protected function validateMethodOptions(): bool
    {
        if ($this->option('raw')) {
            $others = collect(['execute', 'handle', 'invokable', 'construct'])
                ->filter(fn ($option) => $this->option($option));

            if ($others->isNotEmpty()) {
                $this->error("\n⚠️ The --raw option cannot be combined with other options.\n");

                return false;
            }
        }

        $methods = collect(['execute', 'handle', 'invokable'])
            ->filter(fn ($option) => $this->option($option));

        if ($methods->count() > 1) {
            $this->error("\n⚠️ Choose only one method option: --execute, --handle or --invokable.\n");

            return false;
        }

        return true;
    }

    /**
     * Get the method stub based on the selected option.
     */
    protected function getMethodStub(bool $is_raw = false): string
    {
        if ($is_raw) {
            return '//';
        }

        if ($this->option('execute')) {
            return $this->executeMethod();
        }
        if ($this->option('handle')) {
            return $this->handleMethod();
        }
        if ($this->option('invokable')) {
            return $this->invokableMethod();
        }
        if ($this->option('construct')) {
            return $this->constructMethod();
        }

        return match (true) {
            $this->optionOrConfig('action', 'execute') => $this->executeMethod(),
            $this->optionOrConfig('action', 'handle') => $this->handleMethod(),
            $this->optionOrConfig('action', 'invokable') => $this->invokableMethod(),
            $this->optionOrConfig('action', 'construct') => $this->constructMethod(),
            default => '//',
        };
    }

    /**
     * Get the execute method stub.
     */
    protected function executeMethod(): string
    {
        return <<<'PHP'
    public function execute()
        {
            //
        }
    PHP;
    }

    /**
     * Get the handle method stub.
     */
    protected function handleMethod(): string
    {
        return <<<'PHP'
    public function handle()
        {
            //
        }
    PHP;
    }

    /**
     * Get the invokable method stub.
     */
    protected function invokableMethod(): string
    {
        return <<<'PHP'
    public function __invoke()
        {
            //
        }
    PHP;
    }

    /**
     * Get the construct method stub.
     */
    protected function constructMethod(): string
    {
        return <<<'PHP'
    public function __construct()
        {
            //
        }
    PHP;
    }
}
