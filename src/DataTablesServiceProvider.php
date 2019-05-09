<?php

namespace ACFBentveld\DataTables;

use Illuminate\Support\ServiceProvider;

class DataTablesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind('DataTables', 'ACFBentveld\DataTables\DataTables');
    }
}
