<?php

namespace CrCms\Microservice\Dispatching;

class Matcher
{
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
