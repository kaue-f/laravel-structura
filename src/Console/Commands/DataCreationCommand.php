<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class DataCreationCommand extends GeneratorCommand
{
    use InteractsWithCreate;

    protected $signature = 'structura:data {name : Data class name}
                            {--no-final : Disable final class}
                            {--no-readonly : Disable readonly class}
                            {--no-construct : Create Data class without __construct}
                            {--r|raw : Create Data class without modifiers, helpers, or constructor}';

    protected $description = 'Create a new Data class';

    protected $type = 'Data';

    protected function getStub()
    {
        return __DIR__.'/../../../stubs/data.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return config('structura.namespaces.data', $rootNamespace.'\Data');
    }

    protected function getNameInput()
    {
        return Str::finish(trim($this->argument('name')), config('structura.suffixes.data', 'Data'));
    }

    public function handle()
    {
        if ($this->validateMethodOptions() === false) {
            return self::FAILURE;
        }

        return parent::handle();
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        $isRaw = $this->optionOrConfig('data', 'raw');

        return str_replace(
            ['{{imports}}', '{{final}}', '{{readonly}}', '{{implements}}', '{{trait}}', '{{constructor}}'],
            [
                $isRaw ? '' : "\nuse JsonSerializable;\nuse KaueF\Structura\Concerns\InteractsWithData;\n",
                (! $isRaw && ! $this->optionOrConfig('data', 'no-final')) ? 'final ' : '',
                (! $isRaw && ! $this->optionOrConfig('data', 'no-readonly')) ? 'readonly ' : '',
                $isRaw ? '' : ' implements JsonSerializable',
                $isRaw ? '' : "    use InteractsWithData;\n\n",
                (! $isRaw && ! $this->optionOrConfig('data', 'no-construct')) ? $this->constructMethod() : '//',
            ],
            $stub,
        );
    }

    protected function validateMethodOptions(): bool
    {
        if (! $this->option('raw')) {
            return true;
        }

        $options = collect(['no-construct', 'no-final', 'no-readonly'])
            ->filter(fn ($option) => $this->option($option));

        if ($options->isNotEmpty()) {
            $this->error("\n⚠️ The --raw option cannot be combined with other options.\n");

            return false;
        }

        return true;
    }

    protected function constructMethod(): string
    {
        return <<<'PHP'
    public function __construct(
        // Define your Data properties here
    ) {}
PHP;
    }
}
