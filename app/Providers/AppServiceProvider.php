<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prevent the default Laravel 9.x error page from being displayed
        // $this->app['request']->server->set('HTTPS', true);
        // prevent N+1 query
        Model::automaticallyEagerLoadRelationships();
    }
}
