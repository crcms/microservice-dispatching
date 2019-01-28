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

    public function __construct(array $caller)
    {
        $this->caller = $caller;
    }

    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    public function setContainer(Container $container)
    {
        $this->app = $container;
        return $this;
    }

    /**
     * @return mixed
     */
    public function response($request)
    {
        return (new Pipeline($this->app))
            ->send($request)
            ->through($this->getCallerMiddleware())
            ->then(function ($request) {
                return $this->app->call($this->caller['uses']);
            });
    }

    /**
     * @return array
     */
    public function getCaller(): array
    {
        return $this->caller;
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
