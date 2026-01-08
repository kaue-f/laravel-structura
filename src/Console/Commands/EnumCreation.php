<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Console\Command;
use PhpParser\Node\Expr\FuncCall;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class EnumCreation extends Command
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
     * The root namespace for enums.
     *
     * @var string
     */
    protected function namespaceRoot(): string
    {
        return 'App\\Enums';
    }

    /**
     * The type of the console command.
     *
     * @var string
     */
    protected function type(): string
    {
        return 'enum';
    }

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle()
    {
        $this->info("🚀 Creating new action...");

        $name = $this->getClassName($this->argument('name'));
        $path = $this->getPath($name);
        $stub = file_get_contents(filename: __DIR__ . '/../../../stubs/enum.stub');

        $content = str_replace(
            ['{{namespace}}', '{{imports}}', '{{enum}}', '{{trait}}', '{{cases}}', '{{methods}}'],
            [
                $this->getNamespace($name),
                ($this->option('trait')) ? $this->getImportsStub() : '',
                $this->getEnumStub($name),
                ($this->option('trait')) ?  $this->getTraitStub() : '',
                $this->getEnumCases(),
                ($this->option('label')) ? $this->getLabelMethod() : ''
            ],
            $stub
        );

        $this->finishCreation($path, $content);
        return self::SUCCESS;
    }

    /**
     * Get the imports stub based on the selected option.
     * 
     * @return string
     */
    protected function getImportsStub(): string
    {
        return <<<PHP

    use KaueF\Structura\Concerns\InteractsWithEnum;

    PHP;
    }

    /**
     * Get backed enum based on the selected option.
     * 
     * @param string $name
     * @return string
     */
    protected function getEnumStub(string $name): string
    {
        $type = $this->option('backed');
        $enum = class_basename($name);

        if (!$type)
            return $enum;

        if (!in_array($type, ['string', 'int'])) {
            $this->line("$type \n {$this->option('backed')}");
            $this->error("❌ Invalid backed type. Use string or int.");
            exit(self::FAILURE);
        }

        return "{$enum}: {$type}";
    }

    /**
     * Get the trait stub based on the selected option.
     * 
     * @return string
     */
    protected function getTraitStub(): string
    {
        return <<<PHP
        use InteractsWithEnum;

    
    PHP;
    }

    /**
     * Get the enum cases based on the selected option.
     * 
     * @return string
     */
    protected function getEnumCases(): string
    {
        if (! $this->option('cases')) {
            return '    //';
        }

        $backed = $this->option('backed');

        return collect(explode(',', $this->option('cases')))
            ->map(fn($case) => trim($case))
            ->filter()
            ->map(function ($case, $key) use ($backed) {
                $name = $this->normalizeEnumCase($case);

                if (!$backed) {
                    return "    case {$name};";
                }

                $value = match ($backed) {
                    'string' => "'" . strtolower($case) . "'",
                    'int' => $key + 1,
                };

                return "    case {$name} = {$value};";
            })
            ->implode("\n");
    }

    /**
     * Get the label method stub.
     * 
     * @return string
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
     * 
     * @return string
     */
    protected function getMatches(): string
    {
        if (! $this->option('cases')) {
            return "            '' => '',";
        }

        return collect(explode(',', $this->option('cases')))
            ->map(fn($case) => trim($case))
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
     * 
     * @param string $case
     * @return string
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
