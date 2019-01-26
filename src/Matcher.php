<?php

namespace CrCms\Microservice\Dispatching;


use CrCms\Foundation\Transporters\Contracts\DataProviderContract;
use Illuminate\Contracts\Container\Container;

class Matcher
{
    protected $app;

    protected $dispatcher;

    public function __construct(Container $app, Dispatcher $dispatcher)
    {
        $this->app = $app;
        $this->dispatcher = $dispatcher;
    }

    public function match(string $name)
    {
        $caller = $this->dispatcher->getCaller($name);

        return (new Pipeline($this->app))
            ->send([])
            ->through($caller['middleware'])
            ->then(function ($data) use ($caller) {
                $this->app->call($caller['uses']);
            });
    }
}
