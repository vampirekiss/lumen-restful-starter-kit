<?php
namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class ApiMakeCommand extends GeneratorCommand
{
    use DotNameSpaceParser;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new api class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Api';


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/api.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return sprintf('%s\\Http\Api', $rootNamespace);
    }

}