<?php

namespace CrCms\Microservice\Dispatching;

use CrCms\Foundation\Transporters\Contracts\DataProviderContract;
use Illuminate\Contracts\Container\Container;

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
     * @param Dispatcher $dispatcher
     * @param array $caller
     */
    public function __construct(Dispatcher $dispatcher, array $caller)
    {
        $this->dispatcher = $dispatcher;
        $this->caller = $caller;
    }

    /**
     * setContainer
     *
     * @param Container $container
     * @return Matcher
     */
    public function setContainer(Container $container): self
    {
        $this->app = $container;
        return $this;
    }

    /**
     * @return array
     */
    public function getCaller(): array
    {
        return $this->caller;
    }

    /**
     * getCallerUses
     *
     * @return string|\Closure
     */
    public function getCallerUses()
    {
        return $this->caller['uses'];
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
