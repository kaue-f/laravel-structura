<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class DTOCreationCommand extends GeneratorCommand
{
    use InteractsWithCreate;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structura:dto {name : DTO name}
                            {--no-final : Disable final class}
                            {--no-readonly : Disable readonly class}
                            {--no-construct : Create DTO without __construct}
                            {--t|trait : Attach InteractsWithDTO trait}
                            {--r|raw : Create DTO without helpers or modifiers}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new DTO';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'DTO';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../../stubs/dto.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return config('structura.namespaces.dto', $rootNamespace.'\DTOs');
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $name = trim($this->argument('name'));
        $suffix = config('structura.suffixes.dto', 'DTO');

        if ($suffix === 'DTO') {
            $name = Str::replace('Dto', 'DTO', $name);
        }

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

        $is_raw = $this->optionOrConfig('dto', 'raw');

        return str_replace(
            ['{{final}}', '{{readonly}}', '{{trait}}', '{{imports}}', '{{constructor}}'],
            [
                (! $is_raw && ! $this->optionOrConfig('dto', 'no-final')) ? 'final ' : '',
                (! $is_raw && ! $this->optionOrConfig('dto', 'no-readonly')) ? 'readonly ' : '',
                (! $is_raw && $this->optionOrConfig('dto', 'trait')) ? $this->getTraitStub() : '',
                (! $is_raw && $this->optionOrConfig('dto', 'trait')) ? $this->getImportsStub() : '',
                (! $is_raw && ! $this->optionOrConfig('dto', 'no-construct')) ? $this->constructMethod() : '//',
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
        use InteractsWithDTO;

    
    PHP;
    }

    /**
     * Get the imports stub based on the selected option.
     */
    protected function getImportsStub(): string
    {
        return <<<PHP

    use KaueF\Structura\Concerns\InteractsWithDTO;

    PHP;
    }

    /**
     * Get the construct method stub.
     */
    protected function constructMethod(): string
    {
        return <<<'PHP'
        public function __construct(
            // Define your DTO properties here
        ) {}
    PHP;
    }
}
