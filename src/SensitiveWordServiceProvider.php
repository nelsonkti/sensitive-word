<?php

namespace Nelsonkti\SensitiveWord;

use Illuminate\Support\ServiceProvider;

class SensitiveWordServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Nelsonkti\SensitiveWord\Facades\SensitiveWordFacade', function ($app) {
            return 'Nelsonkti\SensitiveWord\SensitiveWord';
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Nelsonkti\SensitiveWord\SensitiveWord', 'SensitiveWord'];
    }
}
