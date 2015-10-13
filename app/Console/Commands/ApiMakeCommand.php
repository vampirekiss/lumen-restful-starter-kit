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

        if ($this->option('repository')) {
            $stub = $this->replaceRepositoryClass($stub, $name);
        }

        var_dump($stub); exit;

        return $stub;
    }

    /**
     * Replace the repository class for the given stub.
     *
     * @param  string $stub
     * @param  string $name
     *
     * @return $this
     */
    protected function replaceRepositoryClass($stub, $name)
    {
        $repoClassName = str_replace('Http\\Api', 'Models', $name) . 'Repository';
        $classParts = explode('\\', $repoClassName);
        $shortRepoClassName = end($classParts);
        $stub = str_replace('ShortRepositoryClass', $shortRepoClassName, $stub);
        return str_replace('RepositoryClass', $repoClassName, $stub);
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