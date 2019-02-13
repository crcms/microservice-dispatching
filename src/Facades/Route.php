<?php

namespace CrCms\Microservice\Dispatching\Facades;

use CrCms\Microservice\Dispatching\Matcher;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $name, $action, $options = [])
 * @method static void single(string $name, $action)
 * @method static self group(array $attributes, \Closure $callback)
 * @method static self namespace(string $namespace)
 * @method static self middleware($middleware)
 * @method static array getCallers()
 * @method static Matcher getCaller(string $name)
 */
class Route extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'caller';
    }
}
