<?php

namespace M3Team\PagedIndex\Console\Commands\Make;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;

class PagedIndexMakeCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:paged_index {name} {--d|database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Paged Index class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'PagedIndex';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {

        return $this->hasOption('database')
            ? $this->resolveStubPath('/stubs/db_paged_index.stub')
            : $this->resolveStubPath('/stubs/paged_index.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param string $stub
     * @return string
     */
    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath : __DIR__ . $stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Http\PagedIndexes';
    }
}