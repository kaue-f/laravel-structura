<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class TraitCreationCommand extends Command
{
    use InteractsWithCreate;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structura:trait {name : Trait name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new trait class';

    /**
     * The root namespace for trait.
     *
     * @var string
     */
    protected function namespaceRoot(): string
    {
        return config('structura.namespaces.trait', 'App\\Concerns');
    }

    /**
     * The type of the console command.
     *
     * @var string
     */
    protected function type(): string
    {
        return 'trait';
    }

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle()
    {
        $this->info("🚀 Creating new trait...");

        $name = $this->getClassName($this->argument('name'));
        $name = Str::replace('Trait', '', $name);

        $path = $this->getPath($name);
        $stub = file_get_contents(__DIR__ . '/../../../stubs/trait.stub');

        $content = str_replace(
            ['{{namespace}}', '{{class}}'],
            [
                $this->getNamespace($name),
                class_basename($name),
            ],
            $stub
        );

        $this->finishCreation($path, $content);
        return self::SUCCESS;
    }
}
