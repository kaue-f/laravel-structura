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
                            {--c|construct : Create an service with a __construct method}
                            {--m|method= : Create a specific method in the service}
                            {--res|result : Format the method to return a ServiceResult}';

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

        $use_result = $this->optionOrConfig('service', 'result');

        $imports = '';
        if ($use_result) {
            $imports = "\nuse KaueF\Structura\Support\ServiceResult;\n";
        }

        return str_replace(
            ['{{imports}}', '{{method}}'],
            [$imports, $this->getMethodStub()],
            $stub
        );
    }

    /**
     * Validate the method options.
     */
    protected function validateMethodOptions(): bool
    {
        return true;
    }

    /**
     * Get the method stub based on the selected option.
     */
    protected function getMethodStub(): string
    {
        $methods = [];

        if ($this->optionOrConfig('service', 'construct')) {
            $methods[] = $this->constructMethod();
        }

        if ($methodName = $this->option('method')) {
            $methods[] = $this->customMethod($methodName);
        }

        return empty($methods) ? '//' : implode("\n\n", $methods);
    }

    /**
     * Get the custom method stub.
     */
    protected function customMethod(string $name): string
    {
        $useResult = $this->optionOrConfig('service', 'result');
        $returnType = $useResult ? ': ServiceResult' : '';
        $body = $useResult ? 'return ServiceResult::success();' : '//';

        return <<<PHP
    public function {$name}(){$returnType}
        {
            {$body}
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
