<?php

namespace CrCms\Microservice\Dispatching;

use ReflectionClass;
use ReflectionMethod;

/**
 * Class ReflectionAction.
 */
class ReflectionControllerMethod
{
    /**
     * @param string $controller
     *
     * @throws \ReflectionException
     *
     * @return array
     */
    public static function getMethods(string $controller): array
    {
        $class = new ReflectionClass($controller);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

        return array_filter(array_map(function (ReflectionMethod $method) {
            return $method->getName();
        }, $methods), function ($value) {
            return strpos($value, '__') !== 0;
        });
    }
}
