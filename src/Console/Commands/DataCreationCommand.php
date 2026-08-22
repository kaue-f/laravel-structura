<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class DataCreationCommand extends GeneratorCommand
{
    use InteractsWithCreate;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structura:data {name : Data class name}
                            {--no-final : Disable final class}
                            {--no-readonly : Disable readonly class}
                            {--no-construct : Create Data class without __construct}
                            {--t|trait : Attach InteractsWithData trait}
                            {--r|raw : Create Data class without helpers or modifiers}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Data class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Data';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../../stubs/data.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return config('structura.namespaces.data', $rootNamespace.'\\Data');
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $name = trim($this->argument('name'));
        $suffix = config('structura.suffixes.data', 'Data');

        return Str::finish($name, $suffix);
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

        $is_raw = $this->optionOrConfig('data', 'raw');

        return str_replace(
            ['{{final}}', '{{readonly}}', '{{trait}}', '{{imports}}', '{{constructor}}'],
            [
                (! $is_raw && ! $this->optionOrConfig('data', 'no-final')) ? 'final ' : '',
                (! $is_raw && ! $this->optionOrConfig('data', 'no-readonly')) ? 'readonly ' : '',
                (! $is_raw && $this->optionOrConfig('data', 'trait')) ? $this->getTraitStub() : '',
                (! $is_raw && $this->optionOrConfig('data', 'trait')) ? $this->getImportsStub() : '',
                (! $is_raw && ! $this->optionOrConfig('data', 'no-construct')) ? $this->constructMethod() : '//',
            ],
            $stub
        );
    }

    /**
     * Validate the method options.
     */
    protected function validateMethodOptions(): bool
    {
        if (! $this->option('raw')) {
            return true;
        }

        $options = collect(['no-construct', 'no-final', 'no-readonly', 'trait'])
            ->filter(fn ($option) => $this->option($option));

        if ($options->isNotEmpty()) {
            $this->error("\n⚠️ The --raw option cannot be combined with other options.\n");

            return false;
        }

        return true;
    }

    /**
     * Get the trait stub based on the selected option.
     */
    protected function getTraitStub(): string
    {
        return <<<'PHP'
        use InteractsWithData;

    
    PHP;
    }

    /**
     * Get the imports stub based on the selected option.
     */
    protected function getImportsStub(): string
    {
        return <<<PHP

    use KaueF\Structura\Concerns\InteractsWithData;

    PHP;
    }

    /**
     * Get the construct method stub.
     */
    protected function constructMethod(): string
    {
        return <<<'PHP'
        public function __construct(
            // Define your Data properties here
        ) {}
    PHP;
    }
}
