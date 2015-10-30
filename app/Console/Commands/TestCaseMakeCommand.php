<?php
namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class TestCaseMakeCommand extends GeneratorCommand
{
    use DotNameSpaceParser;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new api test case class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'TestCase';


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/testcase.stub';
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
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $path = parent::getPath($name);
        return str_replace(
            ['app/Http', '.php'], ['tests/App/Http', 'Test.php'], $path
        );
    }
}