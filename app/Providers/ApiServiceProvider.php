<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Dlap\Api;
use App;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('api',function(){
            return new Api();
        });
    }
}
