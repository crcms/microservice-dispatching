<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2019-01-25 21:53
 *
 * @link http://crcms.cn/
 *
 * @copyright Copyright &copy; 2019 Rights Reserved CRCMS
 */

namespace CrCms\Microservice\Dispatching;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use OutOfBoundsException;

class Dispatcher
{
    /**
     * The application instance.
     *
     * @var Container
     */
    public $app;

    /**
     * The route group attribute stack.
     *
     * @var array
     */
    protected $groupStack = [];

    /**
     * All of the callers waiting to be registered.
     *
     * @var array
     */
    protected $callers = [];

    /**
     * The default is null,
     * which can be an array or a string, but is automatically reset to null after each call.
     *
     * @var string|null|array
     */
    protected $middleware;

    /**
     * The default is null
     * Automatically reset to null after each call
     *
     * @var string|null
     */
    protected $namespace;

    /**
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Register dispatcher
     *
     * @param string $name
     * @param $action
     * @return void
     */
    public function register(string $name, $action, $options = []): void
    {
        if (is_string($action) && strpos($action, '@') === false) {
            $methods = ReflectionControllerMethod::getMethods($action);
            if (isset($options['only'])) {
                $methods = array_intersect($methods, $options['only']);
            } elseif (isset($options['except'])) {
                $methods = array_diff($methods, $options['except']);
            }
            foreach ($methods as $method) {
                $this->single("{$name}.{$method}", "{$action}@{$method}");
            }

            return;
        }

        $this->single($name, $action);
    }

    /**
     * Register single dispatcher
     *
     * @param string $name
     * @param $action
     * @return void
     */
    public function single(string $name, $action): void
    {
        $action = $this->parseAction($action);

        $attributes = null;

        if ($this->hasGroupStack()) {
            $attributes = $this->mergeWithLastGroup([]);
        }

        if (isset($attributes) && is_array($attributes)) {
            $action = $this->mergeGroupAttributes($action, $attributes);
        }

        $this->callers[$name] = $this->createMatcher($action);
    }

    /**
     * Register a set of routes with a set of shared attributes.
     *
     * @param  array $attributes
     * @param  \Closure $callback
     * @return void
     */
    public function group(array $attributes, \Closure $callback)
    {
        $middleware = $this->mergeMiddleware($attributes['middleware'] ?? null);
        if ($middleware) {
            $attributes['middleware'] = $middleware;
        }

        $namespace = $this->mergeNamespace($attributes['namespace'] ?? null);
        if ($namespace) {
            $attributes['namespace'] = $namespace;
        }

        $this->updateGroupStack($attributes);

        call_user_func($callback, $this);

        array_pop($this->groupStack);
    }

    /**
     * Specify the full name space
     * if there is $attributes['namespace'], it will default to the suffix space
     *
     * @param string $namespace
     * @return Dispatcher
     */
    public function namespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Specify middleware with a lower priority than $attributes['middleware']
     *
     * @param string|array $middleware
     * @return $this
     */
    public function middleware($middleware)
    {
        $this->middleware = func_num_args() > 1 ? func_get_args() : $middleware;

        if (is_string($this->middleware)) {
            $this->middleware = explode('|', $this->middleware);
        }

        return $this;
    }

    /**
     * createMatcher
     *
     * @param array $action
     * @return Matcher
     */
    protected function createMatcher(array $action): Matcher
    {
        return new Matcher($this,$action);
    }

    /**
     * Update the group stack with the given attributes.
     *
     * @param  array $attributes
     * @return void
     */
    protected function updateGroupStack(array $attributes)
    {
        if (!empty($this->groupStack)) {
            $attributes = $this->mergeWithLastGroup($attributes);
        }

        $this->groupStack[] = $attributes;
    }

    /**
     * Merge the given group attributes.
     *
     * @param  array $new
     * @param  array $old
     * @return array
     */
    public function mergeGroup($new, $old)
    {
        $new['namespace'] = static::formatUsesPrefix($new, $old);

        return array_merge_recursive(Arr::except($old, ['namespace']), $new);
    }

    /**
     * Merge the given group attributes with the last added group.
     *
     * @param  array $new
     * @return array
     */
    protected function mergeWithLastGroup($new)
    {
        return $this->mergeGroup($new, end($this->groupStack));
    }

    /**
     * Merge and $attributes['middleware'],
     * the priority will be lower than $attributes['middleware']
     *
     * @param $middleware
     * @return array
     */
    protected function mergeMiddleware($middleware): array
    {
        if (is_string($middleware) && !is_null($middleware)) {
            $middleware = explode('|', $middleware);
        }

        $middleware = array_merge((array)$this->middleware, (array)$middleware);

        $this->middleware = null;

        return $middleware;
    }

    /**
     * Merge the namespace attribute in the $attributes array and the namespace attribute in the current object,
     * and clear the current object namespace
     *
     * @param string|null $namespace
     * @return string
     */
    protected function mergeNamespace(?string $namespace = null): string
    {
        if ($this->namespace && strpos($this->namespace, '\\') !== 0) {
            if ($namespace) {
                $namespace = rtrim($this->namespace, '\\').'\\'.ltrim($namespace, '\\');
            }
        }

        $this->namespace = null;

        return strval($namespace);
    }

    /**
     * Format the uses prefix for the new group attributes.
     *
     * @param  array $new
     * @param  array $old
     * @return string|null
     */
    protected static function formatUsesPrefix($new, $old)
    {
        if (isset($new['namespace'])) {
            return isset($old['namespace']) && strpos($new['namespace'], '\\') !== 0
                ? trim($old['namespace'], '\\').'\\'.trim($new['namespace'], '\\')
                : trim($new['namespace'], '\\');
        }

        return $old['namespace'] ?? null;
    }

    /**
     * Parse the action into an array format.
     *
     * @param  mixed $action
     * @return array
     */
    protected function parseAction($action)
    {
        if (is_string($action) || $action instanceof \Closure) {
            $action = ['uses' => $action];
        } elseif (!is_array($action)) {
            $action = [$action];
        }

        $middleware = $this->mergeMiddleware($action['middleware'] ?? null);
        if ($middleware) {
            $action['middleware'] = $middleware;
        }

        return $action;
    }

    /**
     * Determine if the router currently has a group stack.
     *
     * @return bool
     */
    public function hasGroupStack()
    {
        return !empty($this->groupStack);
    }

    /**
     * Merge the group attributes into the action.
     *
     * @param  array $action
     * @param  array $attributes The group attributes
     * @return array
     */
    protected function mergeGroupAttributes(array $action, array $attributes)
    {
        $namespace = $attributes['namespace'] ?? null;
        $middleware = $attributes['middleware'] ?? null;

        return $this->mergeNamespaceGroup(
            $this->mergeMiddlewareGroup(
                $action,
                $middleware),
            $namespace
        );
    }

    /**
     * Merge the namespace group into the action.
     *
     * @param  array $action
     * @param  string $namespace
     * @return array
     */
    protected function mergeNamespaceGroup(array $action, $namespace = null)
    {
        if (isset($namespace, $action['uses'])) {
            $action['uses'] = $this->prependGroupNamespace($action['uses'], $namespace);
        }

        return $action;
    }

    /**
     * Prepend the namespace onto the use clause.
     *
     * @param  string $class
     * @param  string $namespace
     * @return string
     */
    protected function prependGroupNamespace($class, $namespace = null)
    {
        return $namespace !== null && strpos($class, '\\') !== 0
            ? $namespace.'\\'.$class : $class;
    }

    /**
     * Merge the middleware group into the action.
     *
     * @param  array $action
     * @param  array $middleware
     * @return array
     */
    protected function mergeMiddlewareGroup(array $action, $middleware = null)
    {
        if (isset($middleware)) {
            if (isset($action['middleware'])) {
                $action['middleware'] = array_merge($middleware, $action['middleware']);
            } else {
                $action['middleware'] = $middleware;
            }
        }

        return $action;
    }

    /**
     * Get the raw routes for the application.
     *
     * @return array
     */
    public function getCallers()
    {
        return $this->callers;
    }

    /**
     * Find the specified matcher
     *
     * @param string $name
     * @return Matcher
     *
     * @throws OutOfBoundsException
     */
    public function getCaller(string $name): Matcher
    {
        if (!isset($this->callers[$name])) {
            throw new OutOfBoundsException("The dispatching not found match[{$name}]");
        }

        return $this->callers[$name];
    }
}
