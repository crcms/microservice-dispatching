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
        $routePath = $this->app->basePath().'/routes/service.php';
        if (file_exists($routePath)) {
            require $routePath;
        }
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
