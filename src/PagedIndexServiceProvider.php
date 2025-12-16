<?php

namespace M3Team\PagedIndex;

use Illuminate\Support\ServiceProvider;
use M3Team\PagedIndex\Console\Commands\Make\PagedIndexMakeCommand;

class PagedIndexServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/paged-index.php',
            'paged-index'
        );
    }

    /**
     * Bootstrap of the services
     * @return void
     */
    public function boot(): void {
        $this->publishes([
            __DIR__.'/../config/paged-index.php' => config_path('paged-index.php'),
        ]);
        $this->commands([
            PagedIndexMakeCommand::class
        ]);
    }
}
