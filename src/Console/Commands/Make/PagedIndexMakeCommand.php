<?php

namespace M3Team\PagedIndex\Console\Commands\Make;

use Illuminate\Console\GeneratorCommand;

class PagedIndexMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:paged_index {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new application class for paged-index composition';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return base_path('vendor/laravel/framework/src/Illuminate/Foundation/Console/stubs/class.stub');
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
