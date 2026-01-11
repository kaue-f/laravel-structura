<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class DTOCreationCommand extends Command
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
     * The root namespace for dto.
     *
     * @var string
     */
    protected function namespaceRoot(): string
    {
        return config('structura.namespaces.dto', 'App\\DTOs');
    }

    /**
     * The type of the console command.
     *
     * @var string
     */
    protected function type(): string
    {
        return 'dto';
    }

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle()
    {
        $this->validateMethodOptions();
        $this->info("🚀 Creating new DTO...");

        $name = $this->getClassName($this->argument('name'));
        $name = Str::replace('Dto', 'DTO', $name);
        $path = $this->getPath($name);
        $stub = file_get_contents(__DIR__ . '/../../../stubs/dto.stub');

        $is_raw = $this->optionOrConfig('dto', 'raw');

        $content = str_replace(
            ['{{namespace}}', '{{class}}', '{{final}}', '{{readonly}}', '{{trait}}', '{{imports}}', '{{constructor}}'],
            [
                $this->getNamespace($name),
                class_basename($name),
                (!$is_raw && !$this->optionOrConfig('dto', 'no-final')) ? 'final ' : '',
                (!$is_raw && !$this->optionOrConfig('dto', 'no-readonly')) ? 'readonly ' : '',
                (!$is_raw && $this->optionOrConfig('dto', 'trait')) ? $this->getTraitStub() : '',
                (!$is_raw && $this->optionOrConfig('dto', 'trait')) ?  $this->getImportsStub() : '',
                (!$is_raw && !$this->optionOrConfig('dto', 'no-construct')) ? $this->constructMethod() : '//'
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
        if (! $this->option('raw'))
            return;

        $optins = collect(['no-construct', 'no-final', 'no-readonly', 'trait'])
            ->filter(fn($option) => $this->option($option));

        if ($optins->isNotEmpty()) {
            $this->error("\n⚠️ The --raw option cannot be combined with other options.\n");
            exit(self::FAILURE);
        }
    }

    /**
     * Get the trait stub based on the selected option.
     * 
     * @return string
     */
    protected function getTraitStub(): string
    {
        return <<<PHP
        use InteractsWithDTO;

    
    PHP;
    }

    /**
     * Get the imports stub based on the selected option.
     * 
     * @return string
     */
    protected function getImportsStub(): string
    {
        return <<<PHP

    use KaueF\Structura\Concerns\InteractsWithDTO;

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
        public function __construct(
            // Define your DTO properties here
        ) {}
    PHP;
    }
}
