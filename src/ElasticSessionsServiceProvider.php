<?php
namespace ElasticSessions;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class ElasticSessionsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Session::extend('elastic', function($app) {
            return new ElasticSearchSessionHandler;
        });

        $this->publishes([
            __DIR__ . '/Config' => config_path(),
        ]);

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
