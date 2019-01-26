<?php

namespace CrCms\Microservice\Dispatching;

use CrCms\Foundation\Transporters\Contracts\DataProviderContract;
use Illuminate\Contracts\Container\Container;

/**
 *
 */
class Matcher
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var array
     */
    protected $caller;

    /**
     * @param Container $app
     * @param Dispatcher $dispatcher
     */
    public function __construct(Container $app, Dispatcher $dispatcher)
    {
        $this->app = $app;
        $this->dispatcher = $dispatcher;
    }

    /**
     * match
     *
     * @param string $name
     * @param DataProviderContract $data
     * @return mixed
     */
    public function match(string $name, DataProviderContract $data)
    {
        $this->caller = $this->dispatcher->getCaller($name);

        return (new Pipeline($this->app))
            ->send($data)
            ->through($this->caller['middleware'])
            ->then(function ($data) {
                return $this->app->call($this->caller['uses']);
            });
    }

    /**
     * getCallerMiddleware
     *
     * @return array
     */
    public function getCallerMiddleware(): array
    {
        return $this->caller['middleware'] ?? [];
    }
}
