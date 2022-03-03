<?php

namespace M3Team\PagedIndex;

use Illuminate\Support\ServiceProvider;
use M3Team\PagedIndex\Console\Make\PagedIndexMakeCommand;

class PagedIndexServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap of the services
     * @return void
     */
    public function boot()
    {
        $this->commands([
            PagedIndexMakeCommand::class
        ]);
    }
}