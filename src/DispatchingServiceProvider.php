<?php

namespace CrCms\Microservice\Dispatching;

use Illuminate\Support\ServiceProvider;

class DispatchingServiceProvider extends ServiceProvider
{
    /**
     * boot
     *
     * @return void
     */
    public function boot(): void
    {
    }

    /**
     * Register
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerAlias();

        $this->app->singleton('caller', function ($app) {
            return new Dispatcher($app);
        });
    }

    /**
     * registerAlias
     *
     * @return void
     */
    protected function registerAlias(): void
    {
        $this->app->alias('caller', Dispatcher::class);
    }
}
