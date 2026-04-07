<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class ServiceCreationCommand extends GeneratorCommand
{
    use InteractsWithCreate;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structura:service {name : Service name}
                            {--c|construct : Create an service with a __construct method (default)}
                            {--r|raw :  Create an service with without method}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Service';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../../stubs/service.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return config('structura.namespaces.service', $rootNamespace.'\Services');
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
            config('structura.suffixes.service', 'Service')
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

        return str_replace(
            ['{{method}}'],
            [$this->getMethodStub()],
            $stub
        );
    }

    /**
     * Validate the method options.
     */
    protected function validateMethodOptions(): bool
    {
        $methods = collect(['construct', 'raw'])
            ->filter(fn ($option) => $this->option($option));

        if ($methods->count() > 1) {
            $this->error('⚠️ Choose only one option: --construct or --raw.');

            return false;
        }

        return true;
    }

    /**
     * Get the method stub based on the selected option.
     */
    protected function getMethodStub(): string
    {
        return match (true) {
            $this->optionOrConfig('service', 'raw') => '//',
            $this->optionOrConfig('service', 'construct') => $this->constructMethod(),
            default => '//',
        };
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
