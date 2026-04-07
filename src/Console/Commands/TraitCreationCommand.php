<?php

namespace KaueF\Structura\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use KaueF\Structura\Console\Concerns\InteractsWithCreate;

class TraitCreationCommand extends GeneratorCommand
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
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Trait';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../../stubs/trait.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return config('structura.namespaces.trait', $rootNamespace.'\Concerns');
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
            config('structura.suffixes.trait')
        );
    }

    /**
     * Execute the console command.
     *
     * @return int|bool|null
     */
    public function handle()
    {
        return parent::handle();
    }
}
