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
                            {--res|result : Format the method to return a ServiceResult}
                            {--mk|makeable : Attach Makeable trait to allow direct static execution}';

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

        $methodName = $this->option('method');
        $use_result = $methodName !== null && $this->optionOrConfig('service', 'result');
        $use_makeable = $methodName !== null && $this->optionOrConfig('service', 'makeable');

        $imports = '';
        $trait = '';

        if ($use_result) {
            $imports .= "\nuse KaueF\Structura\Support\ServiceResult;";
        }

        if ($use_makeable) {
            $imports .= "\nuse KaueF\Structura\Concerns\Makeable;";
            $trait .= "    use Makeable;\n\n";

            if ($methodName) {
                $trait .= "    protected string \$makeableMethod = '{$methodName}';\n\n";
            }
        }

        if (! empty($imports)) {
            $imports .= "\n";
        }

        return str_replace(
            ['{{imports}}', '{{trait}}', '{{method}}'],
            [$imports, $trait, $this->getMethodStub()],
            $stub
        );
    }

    /**
     * Validate the method options.
     */
    protected function validateMethodOptions(): bool
    {
        $method = $this->option('method');

        if ($method !== null && ! preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $method)) {
            $this->error('⚠️ The --method option must be a valid PHP method name.');

            return false;
        }

        if ($this->option('result') && $method === null) {
            $this->error('⚠️ The --result option requires --method.');

            return false;
        }

        if ($this->option('makeable') && $method === null) {
            $this->error('⚠️ The --makeable option requires --method.');

            return false;
        }

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
