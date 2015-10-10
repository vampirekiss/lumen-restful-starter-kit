<?php
namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class RepositoryMakeCommand extends GeneratorCommand
{
    use DotNameSpaceParser;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent model repository';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $modelName = $this->parseName($this->getNameInput());
        $name = sprintf('%sRepository', $modelName);
        $path = $this->getPath($name);

        if ($this->files->exists($path)) {
            $this->error($this->type . ' already exists!');
            return;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->build($name, $modelName));

        $this->info($this->type . ' created successfully.');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/repository.stub';
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
        return sprintf('%s\\Models', $rootNamespace);
    }

    /**
     * Build the class with the given name.
     *
     * @param  string $name
     * @param  string $modelName
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @return string
     */
    protected function build($name, $modelName)
    {
        $stub = parent::buildClass($name);
        return $this->replaceModelClass($stub, $modelName);
    }

    /**
     * Replace the field for the given stub.
     *
     * @param  string $stub
     * @param  string $modelName
     *
     * @return $this
     */
    protected function replaceModelClass($stub, $modelName)
    {
        return str_replace('ModelClass', $modelName, $stub);
    }

}