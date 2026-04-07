<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class EnumCreationCommand extends GeneratorCommand
{
    use InteractsWithCreate;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structura:enum {name : Enum name}
                            {--backed= : Create backed enum (string|int)}
                            {--cases= : Enum cases (comma separated)}
                            {--l|label : Add label() method}
                            {--t|trait : Attach InteractsWithEnum trait}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new enum';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Enum';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../../stubs/enum.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return config('structura.namespaces.enum', $rootNamespace.'\Enums');
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
            config('structura.suffixes.enum', 'Enum')
        );
    }

    /**
     * Execute the console command.
     *
     * @return int|bool|null
     */
    public function handle()
    {
        // Simple validation directly in handle
        $type = $this->optionValueOrConfig('enum', 'backed');
        if ($type && ! in_array($type, ['string', 'int'])) {
            $this->error('❌ Invalid backed type. Use string or int.');

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
            ['{{imports}}', '{{trait}}', '{{cases}}', '{{methods}}'],
            [
                $this->getImportsStub(),
                ($this->optionOrConfig('enum', 'trait')) ? $this->getTraitStub() : '',
                $this->getEnumCases(),
                '',
            ],
            $this->replaceEnumName($stub, $name)
        );
    }

    /**
     * Replace Enum Name format (e.g. Backed Enum mapping)
     */
    protected function replaceEnumName(string $stub, string $name): string
    {
        $type = $this->optionValueOrConfig('enum', 'backed');
        $enum = class_basename($name);

        $replacement = (! $type) ? $enum : "{$enum}: {$type}";

        return str_replace(['enum '.$enum], ['enum '.$replacement], $stub);
    }

    /**
     * Get the imports stub based on the selected option.
     */
    protected function getImportsStub(): string
    {
        $imports = [];
        if ($this->optionOrConfig('enum', 'trait')) {
            $imports[] = 'use KaueF\Structura\Concerns\InteractsWithEnum;';
        }
        if ($this->optionOrConfig('enum', 'label')) {
            $imports[] = 'use KaueF\Structura\Attributes\Label;';
        }

        if (empty($imports)) {
            return '';
        }

        return "\n".implode("\n", $imports)."\n";
    }

    /**
     * Get the trait stub based on the selected option.
     */
    protected function getTraitStub(): string
    {
        return <<<'PHP'
        use InteractsWithEnum;

    
    PHP;
    }

    /**
     * Get the enum cases based on the selected option.
     */
    protected function getEnumCases(): string
    {
        if (! $this->option('cases')) {
            return '    //';
        }

        $backed = $this->optionValueOrConfig('enum', 'backed');
        $withLabel = $this->optionOrConfig('enum', 'label');

        return collect(explode(',', $this->option('cases')))
            ->map(fn ($case) => trim($case))
            ->filter()
            ->map(function ($case, $key) use ($backed, $withLabel) {
                $name = $this->normalizeEnumCase($case);

                $caseDefinition = '';

                if ($withLabel) {
                    $label = ucwords(strtolower(str_replace(['_', '-'], ' ', $case)));
                    $caseDefinition .= "    #[Label('{$label}')]\n";
                }

                if (! $backed) {
                    $caseDefinition .= "    case {$name};";
                } else {
                    $value = match ($backed) {
                        'string' => "'".strtolower($case)."'",
                        'int' => $key + 1,
                    };
                    $caseDefinition .= "    case {$name} = {$value};";
                }

                return $caseDefinition;
            })
            ->implode($withLabel ? "\n\n" : "\n");
    }

    /**
     * Get the label method stub.
     */
    protected function getLabelMethod(): string
    {
        $matches = $this->getMatches();

        return <<<PHP


        public function label(): string
        {
             return match (\$this) {
    {$matches}
             };
        }
    PHP;
    }

    /**
     * Get the matches based on the selected option.
     */
    protected function getMatches(): string
    {
        if (! $this->option('cases')) {
            return "            '' => '',";
        }

        return collect(explode(',', $this->option('cases')))
            ->map(fn ($case) => trim($case))
            ->filter()
            ->map(function ($case) {
                $self = $this->normalizeEnumCase($case);
                $label = ucwords(strtolower(str_replace(['_', '-'], ' ', $case)));

                return "            self::{$self} => '{$label}',";
            })
            ->implode("\n");
    }

    /**
     * Normalize the enum case.
     */
    protected function normalizeEnumCase(string $case): string
    {
        return str_replace(
            ' ',
            '',
            ucwords(
                str_replace(['_', '-'], ' ', strtolower(trim($case)))
            )
        );
    }
}
