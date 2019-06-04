<?php

namespace SingleQuote\DataTables;

use Illuminate\Support\ServiceProvider;

class DataTableServiceProvider extends ServiceProvider
{

    /**
     * Commands
     *
     * @var array
     */
    protected $commands = [
        \SingleQuote\DataTables\Commands\MakeModel::class,
        \SingleQuote\DataTables\Commands\MakeField::class,
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //translations
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'datatables');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind('DataTable', 'SingleQuote\DataTables\DataTable');

        //where the views are
        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel-datatables');

        //register the commands
        $this->commands($this->commands);
    }
}
