<?php


namespace Isofman\LaravelRestier;


use Illuminate\Support\ServiceProvider;

class RestierServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('restier', function() {
            return new Restier;
        });
    }

    public function boot()
    {
        // Load helpers
        require_once(__DIR__ . '/helpers.php');
    }
}