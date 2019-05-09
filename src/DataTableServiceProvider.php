<?php

namespace SingleQuote\DataTables;

use Illuminate\Support\ServiceProvider;

class DataTableServiceProvider extends ServiceProvider
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
        $this->app->bind('DataTable', 'SingleQuote\DataTables\DataTable');

        //where the views are
        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel-datatables');
    }
}
