<?php
namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

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
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if (parent::fire() !== false) {
            if ($this->option('model')) {
                $this->call('make:model', ['name' => $this->argument('name')]);
                $table = str_replace('._', '_', Str::plural(Str::snake(class_basename($this->argument('name')))));
                $this->call('make:migration', ['name' => "create_{$table}_table", '--create' => $table]);
            }
        }
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        list($resourceStub, $actionStub) = explode('#split', $stub);

        if ($this->option('model')) {
            $modelClass = Str::singular(str_replace('.', '\\', $this->getNameInput()));
            $resourceStub = str_replace(
                ['DummyModelClass'], [$modelClass], $resourceStub
            );
            return trim($resourceStub);
        }

        return trim($actionStub);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return sprintf('%s\\Http\Api', $rootNamespace);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_NONE, 'Create a new model file for the api.']
        ];
    }

}